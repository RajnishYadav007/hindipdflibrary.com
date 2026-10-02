<?php

namespace App\Services\Archive;

use App\Models\Audiobook;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ArchiveOrgService
{
    /**
     * Cache TTL in seconds (24 hours).
     */
    protected const CACHE_TTL = 86400;

    /**
     * Helper to get a configured HTTP client without strict SSL verification (works on all environments).
     */
    protected function http(int $timeout = 10)
    {
        return Http::withoutVerifying()->timeout($timeout);
    }

    /**
     * Extract canonical Archive.org item identifier from a URL or raw identifier string.
     */
    public function extractIdentifier(?string $urlOrId): ?string
    {
        if (empty($urlOrId)) {
            return null;
        }

        $trimmed = trim($urlOrId);

        // Pattern matching standard Archive.org URLs:
        // https://archive.org/details/identifier
        // https://archive.org/stream/identifier
        // https://archive.org/download/identifier/file.pdf
        if (preg_match('#archive\.org/(?:details|stream|download|embed)/([a-zA-Z0-9_\-\.]+)#i', $trimmed, $matches)) {
            return $matches[1];
        }

        // Direct identifier format (e.g. "godan00prem" or "in.ernet.dli.2015.405490")
        if (preg_match('/^[a-zA-Z0-9_\-\.]+$/', $trimmed) && ! str_starts_with($trimmed, 'http')) {
            return $trimmed;
        }

        return null;
    }

    /**
     * Check if a given URL or identifier is an Archive.org reference.
     */
    public function isArchiveUrl(?string $url): bool
    {
        return $this->extractIdentifier($url) !== null;
    }

    /**
     * Get the official Archive.org embedded reader URL for an item.
     */
    public function getEmbedUrl(string $identifier): string
    {
        return "https://archive.org/embed/{$identifier}?ui=embed#mode/1up";
    }

    /**
     * Get the Archive.org details page URL for an item.
     */
    public function getDetailsUrl(string $identifier): string
    {
        return "https://archive.org/details/{$identifier}";
    }

    /**
     * Get book manifest.
     *
     * @return array{
     *     identifier: string,
     *     title: string,
     *     reader_mode: string,
     *     embed_url: string,
     *     details_url: string,
     *     page_count: int,
     *     pages: array,
     *     has_ocr: bool
     * }|null
     */
    public function getManifest(string $identifier): ?array
    {
        $identifier = $this->extractIdentifier($identifier) ?: $identifier;
        if (empty($identifier)) {
            return null;
        }

        $cacheKey = "archive_manifest_v4_{$identifier}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($identifier) {
            return $this->fetchManifestFromArchive($identifier);
        });
    }

    /**
     * Fetch manifest and page data directly from Archive.org open APIs.
     */
    protected function fetchManifestFromArchive(string $identifier): ?array
    {
        $embedUrl = $this->getEmbedUrl($identifier);
        $detailsUrl = $this->getDetailsUrl($identifier);

        try {
            // Fetch metadata JSON
            $metaUrl = "https://archive.org/metadata/{$identifier}";
            $metaResponse = $this->http(10)->get($metaUrl);

            if (! $metaResponse->successful() || empty($metaResponse->json())) {
                return $this->buildEmbedManifest($identifier, $embedUrl, $detailsUrl);
            }

            $data = $metaResponse->json();
            $meta = $data['metadata'] ?? [];
            $files = $data['files'] ?? [];

            $title = is_array($meta['title'] ?? null) ? ($meta['title'][0] ?? Str::headline($identifier)) : ($meta['title'] ?? Str::headline($identifier));

            $pageCount = 0;
            if (! empty($meta['imagecount'])) {
                $pageCount = (int) (is_array($meta['imagecount']) ? $meta['imagecount'][0] : $meta['imagecount']);
            }

            $hasPageScans = false;
            foreach ($files as $file) {
                $name = $file['name'] ?? '';
                $format = strtolower($file['format'] ?? '');

                if (str_ends_with($name, '_jp2.zip') || $format === 'jpeg 2000' || str_ends_with($name, '.djvu')) {
                    $hasPageScans = true;
                    break;
                }
            }

            if (! $hasPageScans) {
                return $this->buildEmbedManifest($identifier, $embedUrl, $detailsUrl, $title, $pageCount ?: 0);
            }

            return $this->fetchScanManifest($identifier, $title, $pageCount, $embedUrl, $detailsUrl);

        } catch (\Throwable $e) {
            return $this->buildEmbedManifest($identifier, $embedUrl, $detailsUrl);
        }
    }

    /**
     * Fetch page scan manifest.
     */
    protected function fetchScanManifest(
        string $identifier,
        string $title,
        int $pageCount,
        string $embedUrl,
        string $detailsUrl
    ): array {
        try {
            if ($pageCount <= 0) {
                $pageCount = 50;
            }

            $displayPages = min($pageCount, 100);
            $ocrTextPages = $this->fetchPageTextSample($identifier, $displayPages);

            $pages = [];
            for ($i = 1; $i <= $displayPages; $i++) {
                $n = $i - 1;
                $pages[] = [
                    'page_number' => $i,
                    'image_url' => "https://archive.org/download/{$identifier}/page/n{$n}_medium.jpg",
                    'large_image_url' => "https://archive.org/download/{$identifier}/page/n{$n}_large.jpg",
                    'text' => $ocrTextPages[$i] ?? "पृष्ठ {$i} का पाठ (Page {$i})",
                ];
            }

            return [
                'identifier' => $identifier,
                'title' => $title,
                'reader_mode' => 'scans',
                'embed_url' => $embedUrl,
                'details_url' => $detailsUrl,
                'page_count' => $pageCount,
                'pages' => $pages,
                'has_ocr' => ! empty($ocrTextPages),
            ];

        } catch (\Throwable) {
            return $this->buildEmbedManifest($identifier, $embedUrl, $detailsUrl, $title, $pageCount);
        }
    }

    /**
     * Build an embed-mode manifest.
     */
    protected function buildEmbedManifest(
        string $identifier,
        string $embedUrl,
        string $detailsUrl,
        string $title = '',
        int $pageCount = 0
    ): array {
        return [
            'identifier' => $identifier,
            'title' => $title ?: Str::headline($identifier),
            'reader_mode' => 'embed',
            'embed_url' => $embedUrl,
            'details_url' => $detailsUrl,
            'page_count' => $pageCount,
            'pages' => [],
            'has_ocr' => false,
        ];
    }

    /**
     * Fetch and parse text sample from Archive.org's _djvu.txt file.
     *
     * @return array<int, string>
     */
    protected function fetchPageTextSample(string $identifier, int $maxPages): array
    {
        try {
            $txtUrl = "https://archive.org/stream/{$identifier}/{$identifier}_djvu.txt";
            $response = $this->http(6)->get($txtUrl);

            if (! $response->successful()) {
                return [];
            }

            $fullText = $response->body();
            if (empty($fullText)) {
                return [];
            }

            $rawPages = explode("\x0C", $fullText);
            if (count($rawPages) < 2) {
                $rawPages = preg_split('/(?:\r?\n){3,}/', $fullText);
            }

            $pageTexts = [];
            $index = 1;
            foreach ($rawPages as $pageStr) {
                $cleaned = trim(preg_replace('/\s+/', ' ', $pageStr));
                if (! empty($cleaned)) {
                    $pageTexts[$index] = mb_substr($cleaned, 0, 1500);
                    $index++;
                    if ($index > $maxPages) {
                        break;
                    }
                }
            }

            return $pageTexts;
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Get the full book text split into word-count pages — powers read & audiobook modes.
     *
     * @return array<int, array{page: int, text: string}> Array of pages (1-indexed)
     */
    public function getBookTextPages(string $identifier, int $wordsPerPage = 250): array
    {
        $identifier = $this->extractIdentifier($identifier) ?: $identifier;
        if (empty($identifier)) {
            return [];
        }

        $cacheKey = "archive_text_pages_v5_{$identifier}_{$wordsPerPage}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($identifier, $wordsPerPage) {
            return $this->buildTextPages($identifier, $wordsPerPage);
        });
    }

    /**
     * Build text pages by fetching from Archive.org.
     *
     * @return array<int, array{page: int, text: string}>
     */
    protected function buildTextPages(string $identifier, int $wordsPerPage): array
    {
        $fullText = $this->fetchFullText($identifier);

        if (empty($fullText) || mb_strlen($fullText) < 80) {
            return [];
        }

        // Verify that the text contains authentic Devanagari Hindi characters
        // Corrupted OCR files (like old Abbyy Finereader without Hindi pack) produce only ASCII noise
        preg_match_all('/[\x{0900}-\x{097F}]/u', $fullText, $devaMatches);
        $devaCount = count($devaMatches[0]);
        if (mb_strlen($fullText) > 300 && $devaCount < 50) {
            // Text is not genuine Devanagari Hindi — reject garbled OCR gibberish
            return [];
        }

        // Clean up text
        $fullText = preg_replace('/\x0C/', "\n\n", $fullText);
        $fullText = preg_replace('/[ \t]+/', ' ', $fullText);
        $fullText = preg_replace('/\n{4,}/', "\n\n", $fullText);
        $fullText = trim($fullText);

        $words = preg_split('/\s+/', $fullText, -1, PREG_SPLIT_NO_EMPTY);
        if (empty($words)) {
            return [];
        }

        $pages = [];
        $pageNumber = 1;
        $chunk = [];

        foreach ($words as $word) {
            $chunk[] = $word;
            if (count($chunk) >= $wordsPerPage) {
                $pages[] = [
                    'page' => $pageNumber,
                    'text' => implode(' ', $chunk),
                ];
                $pageNumber++;
                $chunk = [];
                // Cap at 300 pages to keep payload reasonable
                if ($pageNumber > 300) {
                    break;
                }
            }
        }

        if (! empty($chunk)) {
            $pages[] = [
                'page' => $pageNumber,
                'text' => implode(' ', $chunk),
            ];
        }

        return $pages;
    }

    /**
     * Fetch the full plain text of a book from Archive.org.
     */
    protected function fetchFullText(string $identifier): string
    {
        // 1. Try standard predictable URLs
        $candidates = [
            "https://archive.org/stream/{$identifier}/{$identifier}_djvu.txt",
            "https://archive.org/download/{$identifier}/{$identifier}_djvu.txt",
            "https://archive.org/download/{$identifier}/{$identifier}.txt",
        ];

        foreach ($candidates as $url) {
            try {
                $response = $this->http(12)->get($url);
                if ($response->successful()) {
                    $body = trim($response->body());
                    if (mb_strlen($body) > 80 && ! str_starts_with($body, '<!') && ! str_starts_with(strtolower($body), '<html')) {
                        return $body;
                    }
                }
            } catch (\Throwable) {
                continue;
            }
        }

        // 2. Discover text files via Archive.org files metadata API
        try {
            $metaResponse = $this->http(10)->get("https://archive.org/metadata/{$identifier}/files");
            if ($metaResponse->successful()) {
                $data = $metaResponse->json();
                $files = $data['result'] ?? ($data['files'] ?? $data);
                if (is_array($files)) {
                    foreach ($files as $file) {
                        $name = $file['name'] ?? '';
                        $format = strtolower($file['format'] ?? '');
                        if (($format === 'djvutxt' || str_contains($format, 'text') || str_ends_with($name, '_djvu.txt') || str_ends_with($name, '.txt'))
                            && ! str_contains($name, 'scandata')
                            && ! str_ends_with($name, '.xml')) {
                            $txtUrl = "https://archive.org/download/{$identifier}/{$name}";
                            $response = $this->http(15)->get($txtUrl);
                            if ($response->successful()) {
                                $body = trim($response->body());
                                if (mb_strlen($body) > 80 && ! str_starts_with($body, '<!') && ! str_starts_with(strtolower($body), '<html')) {
                                    return $body;
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Throwable) {
            // Ignore
        }

        return '';
    }

    /**
     * Alias for extractIdentifier to fulfill resolveItemIdentifier contract.
     */
    public function resolveItemIdentifier(?string $urlOrId): ?string
    {
        return $this->extractIdentifier($urlOrId);
    }

    /**
     * Check if a URL points directly to a supported audio file format.
     */
    public function isDirectAudioUrl(?string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH) ?? '';

        return (bool) preg_match('/\.(mp3|m4a|ogg|wav|aac|flac)$/i', $path);
    }

    /**
     * Fetch complete item metadata from Archive.org with caching.
     */
    public function fetchMetadata(string $identifier): ?array
    {
        $id = $this->extractIdentifier($identifier) ?: $identifier;
        if (empty($id)) {
            return null;
        }

        $cacheKey = "archive_meta_v2_{$id}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            try {
                $response = $this->http(15)->get("https://archive.org/metadata/{$id}");
                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Throwable $e) {
                Log::warning("ArchiveOrgService: Failed to fetch metadata for {$id}: ".$e->getMessage());
            }

            return null;
        });
    }

    /**
     * Build canonical direct download URL for an item's file.
     */
    public function buildDirectDownloadUrl(string $identifier, string $fileName): string
    {
        $id = $this->extractIdentifier($identifier) ?: $identifier;

        return "https://archive.org/download/{$id}/".rawurlencode($fileName);
    }

    /**
     * Discover and filter candidate audio files from an Archive.org item.
     *
     * @return array<int, array>
     */
    public function getAudioFiles(string $identifier): array
    {
        $data = $this->fetchMetadata($identifier);
        if (! $data || empty($data['files'])) {
            return [];
        }

        $files = $data['files'];
        $audioCandidates = [];

        foreach ($files as $f) {
            $name = $f['name'] ?? '';
            $lower = strtolower($name);

            // Filter supported audio formats and exclude spectrograms, waveforms, or metadata derivatives
            if ((str_ends_with($lower, '.mp3') || str_ends_with($lower, '.m4a') || str_ends_with($lower, '.ogg'))
                && ! str_contains($lower, 'spectrogram')
                && ! str_contains($lower, '.afpk')
                && ! str_contains($lower, '.png')) {
                $audioCandidates[] = $f;
            }
        }

        return $audioCandidates;
    }

    /**
     * Normalize, filter, and naturally sort audio tracks from Archive.org.
     * Selects standard 128kbps quality over 64kbps derivatives, cleans titles,
     * and guarantees numeric track ordering.
     *
     * @return array<int, array{
     *     track_number: int,
     *     source_file_name: string,
     *     title: string,
     *     duration_seconds: ?int,
     *     size: ?int,
     *     mime_type: string,
     *     direct_url: string
     * }>
     */
    public function normalizeAudioFiles(array $files, string $identifier): array
    {
        $id = $this->extractIdentifier($identifier) ?: $identifier;
        if (empty($files) || empty($id)) {
            return [];
        }

        $tracks = [];

        foreach ($files as $f) {
            $name = $f['name'] ?? '';
            $lower = strtolower($name);

            if (! (str_ends_with($lower, '.mp3') || str_ends_with($lower, '.m4a') || str_ends_with($lower, '.ogg'))
                || str_contains($lower, 'spectrogram')
                || str_contains($lower, '.afpk')
                || str_contains($lower, '.png')) {
                continue;
            }

            $format = $f['format'] ?? '';
            $title = $f['title'] ?? '';
            $trackRaw = $f['track'] ?? null;

            // Determine track number
            $trackNum = null;
            if ($trackRaw !== null && is_numeric(explode('/', (string) $trackRaw)[0])) {
                $trackNum = (int) explode('/', (string) $trackRaw)[0];
            } elseif (preg_match('/[_-](\d{1,3})[_\.\-]/', $name, $m)) {
                $trackNum = (int) $m[1];
            } elseif (preg_match('/^(\d{1,3})/', $title, $m)) {
                $trackNum = (int) $m[1];
            }

            $key = $trackNum ? "track_{$trackNum}" : $name;

            // Quality score: 128kbps = 2, VBR/standard = 1, 64kbps = 0
            $weight = 1;
            if (str_contains(strtolower($name), '128kb') || str_contains(strtolower($format), '128kbps')) {
                $weight = 2;
            } elseif (str_contains(strtolower($format), '64kbps') || (str_ends_with(strtolower($name), '.mp3') && ! str_contains(strtolower($name), '128kb') && ! str_contains(strtolower($name), 'vbr'))) {
                $weight = 0;
            }

            if (! isset($tracks[$key]) || $weight > $tracks[$key]['weight']) {
                $cleanTitle = $title ?: pathinfo($name, PATHINFO_FILENAME);
                // Clean leading track numbers and prefixes like "01 - ", "01 - Chapter 1- ", etc.
                $cleanTitle = preg_replace('/^\d{1,3}\s*[-–:]\s*/u', '', $cleanTitle);
                $cleanTitle = preg_replace('/^Chapter\s*\d{1,3}\s*[-–:]\s*/ui', '', $cleanTitle);

                $tracks[$key] = [
                    'track_number' => $trackNum,
                    'source_file_name' => $name,
                    'title' => trim($cleanTitle),
                    'duration_seconds' => ! empty($f['length']) ? (int) round((float) $f['length']) : null,
                    'size' => ! empty($f['size']) ? (int) $f['size'] : null,
                    'mime_type' => 'audio/mpeg',
                    'direct_url' => $this->buildDirectDownloadUrl($id, $name),
                    'weight' => $weight,
                ];
            }
        }

        // Sort by track number naturally
        uasort($tracks, function ($a, $b) {
            if ($a['track_number'] !== null && $b['track_number'] !== null) {
                return $a['track_number'] <=> $b['track_number'];
            }

            return strnatcasecmp($a['source_file_name'], $b['source_file_name']);
        });

        $result = array_values($tracks);

        // Guarantee sequential 1-indexed track numbers
        for ($i = 0; $i < count($result); $i++) {
            if (! $result[$i]['track_number']) {
                $result[$i]['track_number'] = $i + 1;
            }
            unset($result[$i]['weight']);
        }

        return $result;
    }

    /**
     * Resolve direct playable audio URL.
     * If already a direct audio stream, returns it immediately.
     * If an Archive.org item details or landing URL, discovers the first normalized audio track.
     */
    public function resolveDirectAudioUrl(?string $urlOrId): ?string
    {
        if (empty($urlOrId)) {
            return null;
        }

        if ($this->isDirectAudioUrl($urlOrId)) {
            return $urlOrId;
        }

        $identifier = $this->extractIdentifier($urlOrId);
        if (! $identifier) {
            return null;
        }

        $files = $this->getAudioFiles($identifier);
        $normalized = $this->normalizeAudioFiles($files, $identifier);

        return $normalized[0]['direct_url'] ?? null;
    }

    /**
     * Synchronize an Audiobook and its chapters from an Archive.org item.
     *
     * Ingestion Pipeline Guarantees:
     * 1. Runs within an atomic DB::transaction() with full rollback on failure.
     * 2. Idempotent: matches existing chapters by source_file_name or chapter_number.
     * 3. Natural numeric ordering of all tracks.
     * 4. Does not automatically assign 'Public Domain' unless verified in metadata.
     * 5. Updates parent duration to the sum of chapter durations.
     *
     * @return int Number of chapters synchronized
     *
     * @throws \Exception
     */
    public function syncAudiobookFromArchive(Audiobook $audiobook, string $archiveIdentifier): int
    {
        $identifier = $this->extractIdentifier($archiveIdentifier) ?: $archiveIdentifier;
        if (empty($identifier)) {
            throw new \InvalidArgumentException("Invalid Archive.org identifier: {$archiveIdentifier}");
        }

        $metaData = $this->fetchMetadata($identifier);
        if (! $metaData) {
            throw new \RuntimeException("Unable to retrieve metadata from Archive.org for identifier: {$identifier}");
        }

        $files = $metaData['files'] ?? [];
        $normalizedTracks = $this->normalizeAudioFiles($files, $identifier);

        if (empty($normalizedTracks)) {
            throw new \RuntimeException("No valid audio tracks found for Archive.org item: {$identifier}");
        }

        $meta = $metaData['metadata'] ?? [];

        return DB::transaction(function () use ($audiobook, $identifier, $meta, $normalizedTracks) {
            $totalDuration = 0;
            $syncedCount = 0;

            // Verify rights / license metadata without assuming Public Domain blindly
            $licenseUrl = is_array($meta['licenseurl'] ?? null) ? ($meta['licenseurl'][0] ?? null) : ($meta['licenseurl'] ?? null);
            $rights = is_array($meta['rights'] ?? null) ? ($meta['rights'][0] ?? null) : ($meta['rights'] ?? null);
            $collection = is_array($meta['collection'] ?? null) ? implode(' ', $meta['collection']) : ($meta['collection'] ?? '');

            $isVerifiedPublicDomain = str_contains(strtolower((string) $licenseUrl), 'publicdomain')
                || str_contains(strtolower((string) $rights), 'public domain')
                || str_contains(strtolower($collection), 'librivox')
                || str_contains(strtolower((string) ($meta['creator'] ?? '')), 'librivox');

            $copyrightStatus = $isVerifiedPublicDomain ? 'Public Domain' : ($audiobook->copyright_status ?: 'Pending Review');
            $licenseType = $licenseUrl ?: ($rights ?: ($isVerifiedPublicDomain ? 'Public Domain / Free Audio' : ($audiobook->license_type ?: 'Unknown / Pending Review')));

            // Prune any obsolete chapters that are not in the normalized tracks list
            $validChapterNumbers = array_column($normalizedTracks, 'track_number');
            $audiobook->chapters()->whereNotIn('chapter_number', $validChapterNumbers)->delete();

            foreach ($normalizedTracks as $track) {
                $chapterNum = $track['track_number'];
                $trackDuration = $track['duration_seconds'] ?? 0;
                $totalDuration += $trackDuration;

                // Idempotently match chapter by source_file_name or chapter_number
                $chapter = $audiobook->chapters()
                    ->where(function ($q) use ($track, $chapterNum) {
                        $q->where('source_file_name', $track['source_file_name'])
                            ->orWhere('chapter_number', $chapterNum);
                    })
                    ->first();

                if (! $chapter) {
                    $chapter = $audiobook->chapters()->create([
                        'chapter_number' => $chapterNum,
                        'duration_seconds' => $trackDuration ?: null,
                        'external_audio_url' => $track['direct_url'],
                        'source_file_name' => $track['source_file_name'],
                        'mime_type' => $track['mime_type'],
                        'status' => 'published',
                        'sort_order' => $chapterNum,
                    ]);
                } else {
                    $chapter->update([
                        'chapter_number' => $chapterNum,
                        'duration_seconds' => $trackDuration ?: null,
                        'external_audio_url' => $track['direct_url'],
                        'source_file_name' => $track['source_file_name'],
                        'mime_type' => $track['mime_type'],
                        'status' => 'published',
                        'sort_order' => $chapterNum,
                    ]);
                }

                // Synchronize translations for chapter title
                $chapter->translations()->updateOrCreate(
                    ['locale' => 'hi'],
                    ['title' => "अध्याय {$chapterNum}: ".$track['title']]
                );

                $chapter->translations()->updateOrCreate(
                    ['locale' => 'en'],
                    ['title' => "Chapter {$chapterNum}: ".$track['title']]
                );

                $syncedCount++;
            }

            // Update parent Audiobook record
            $audiobook->update([
                'duration_seconds' => $totalDuration > 0 ? $totalDuration : $audiobook->duration_seconds,
                'source_type' => 'archive_org',
                'source_url' => "https://archive.org/details/{$identifier}",
                'copyright_status' => $copyrightStatus,
                'license_type' => $licenseType,
            ]);

            return $syncedCount;
        });
    }
}
