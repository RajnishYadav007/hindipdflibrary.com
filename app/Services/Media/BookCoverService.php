<?php

namespace App\Services\Media;

use App\Models\Book;
use App\Services\Archive\ArchiveOrgService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookCoverService
{
    public function __construct(
        protected ArchiveOrgService $archiveService
    ) {}

    /**
     * Automatically generate or fetch a cover image for a book from its PDF or Archive.org URL.
     */
    public function autoGenerateCover(Book $book, ?string $uploadedPdfRelativePath = null): ?string
    {
        $slug = $book->slug ?: 'book';

        // 1. Try extracting the front cover from an uploaded or existing PDF file
        $pdfPath = $uploadedPdfRelativePath;
        if (empty($pdfPath)) {
            $pdfMedia = $book->media()->where('collection_name', 'pdf')->first();
            if ($pdfMedia) {
                $pdfPath = $pdfMedia->file_name;
            }
        }

        if (! empty($pdfPath)) {
            $fullPdfPath = Storage::disk('private_pdfs')->path($pdfPath);
            if (! file_exists($fullPdfPath)) {
                $fullPdfPath = Storage::disk('public')->path($pdfPath);
            }

            if (file_exists($fullPdfPath)) {
                $cover = $this->extractCoverFromPdf($fullPdfPath, $slug);
                if ($cover) {
                    return $cover;
                }
            }
        }

        // 2. Try fetching the cover from an Archive.org URL
        $sourceUrl = $book->source_url ?: $book->archive_org_identifier;
        if (! empty($sourceUrl)) {
            $cover = $this->fetchCoverFromArchive($sourceUrl, $slug);
            if ($cover) {
                return $cover;
            }
        }

        // 3. Fallback: Generate an elegant typographic book cover using GD
        $authorName = $book->authors->first()?->translations->where('locale', 'hi')->first()?->name
            ?? $book->authors->first()?->name ?? '';

        return $this->generateTypographicCover($book->title, $authorName, $slug);
    }

    /**
     * Extract the first embedded image/cover from a PDF document.
     * Uses pure PHP stream extraction (works on shared hosting & VPS without external binaries),
     * with an optional fallback to pdftoppm if present.
     */
    public function extractCoverFromPdf(string $pdfAbsolutePath, string $slug = 'book'): ?string
    {
        if (! file_exists($pdfAbsolutePath) || ! is_readable($pdfAbsolutePath)) {
            return null;
        }

        // Strategy A: If pdftoppm is available on host, render page 1 at high DPI
        if ($this->hasBinary('pdftoppm')) {
            $cliCover = $this->extractViaPdftoppm($pdfAbsolutePath, $slug);
            if ($cliCover) {
                return $cliCover;
            }
        }

        // Strategy B: Pure PHP stream extraction of the first embedded JPEG stream
        try {
            $pdfContent = file_get_contents($pdfAbsolutePath);
            if (empty($pdfContent)) {
                return null;
            }

            // Search for JPEG SOI marker \xFF\xD8\xFF
            $offset = 0;
            $len = strlen($pdfContent);

            while (($soiPos = strpos($pdfContent, "\xFF\xD8\xFF", $offset)) !== false) {
                // Find matching EOI marker \xFF\xD9 after SOI
                $eoiPos = strpos($pdfContent, "\xFF\xD9", $soiPos);
                if ($eoiPos === false) {
                    $offset = $soiPos + 3;

                    continue;
                }

                $imgLength = ($eoiPos - $soiPos) + 2;
                // Plausible cover size is at least 1 KB and validated by width & height
                if ($imgLength > 1024) {
                    $jpgBytes = substr($pdfContent, $soiPos, $imgLength);
                    $img = @imagecreatefromstring($jpgBytes);

                    if ($img !== false) {
                        $w = imagesx($img);
                        $h = imagesy($img);
                        imagedestroy($img);

                        // Book cover aspect ratio: height must be reasonable and dimensions > 180px
                        if ($w >= 180 && $h >= 240) {
                            $filename = 'covers/cover_'.$slug.'_'.Str::random(10).'.jpg';
                            Storage::disk('public')->put($filename, $jpgBytes);

                            return $filename;
                        }
                    }
                }

                $offset = $eoiPos + 2;
                // Only scan up to first 2MB of PDF to keep execution fast
                if ($offset > 2097152) {
                    break;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Pure PHP PDF cover extraction failed: '.$e->getMessage(), ['path' => $pdfAbsolutePath]);
        }

        return null;
    }

    /**
     * Fetch cover scan from Archive.org open APIs.
     */
    public function fetchCoverFromArchive(string $sourceUrl, string $slug = 'book'): ?string
    {
        $identifier = $this->archiveService->extractIdentifier($sourceUrl) ?: trim($sourceUrl);
        if (empty($identifier)) {
            return null;
        }

        // Candidate URLs in priority order: full-res cover scan -> page 0 scan -> thumbnail
        $candidates = [
            "https://archive.org/download/{$identifier}/page/cover_t.jpg",
            "https://archive.org/download/{$identifier}/page/n0.jpg",
            "https://archive.org/services/img/{$identifier}",
        ];

        foreach ($candidates as $url) {
            try {
                $response = Http::withoutVerifying()
                    ->timeout(12)
                    ->get($url);

                if ($response->successful() && strlen($response->body()) > 2048) {
                    $body = $response->body();
                    $img = @imagecreatefromstring($body);

                    if ($img !== false) {
                        imagedestroy($img);
                        $filename = 'covers/archive_'.$slug.'_'.Str::random(10).'.jpg';
                        Storage::disk('public')->put($filename, $body);

                        return $filename;
                    }
                }
            } catch (\Throwable $e) {
                Log::debug("Archive cover candidate {$url} failed: ".$e->getMessage());
            }
        }

        return null;
    }

    /**
     * Generate an elegant typographic book cover using PHP GD when no image is available.
     */
    public function generateTypographicCover(string $title, ?string $author = null, string $slug = 'book'): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $width = 600;
        $height = 900;
        $im = imagecreatetruecolor($width, $height);

        if (! $im) {
            return null;
        }

        // Palette: Deep indigo-navy background with warm gold framing
        $bg = imagecolorallocate($im, 15, 23, 42); // #0f172a
        $gold = imagecolorallocate($im, 217, 119, 6); // #d97706
        $goldBorder = imagecolorallocate($im, 180, 83, 9); // #b45309
        $white = imagecolorallocate($im, 255, 255, 255);
        $slate = imagecolorallocate($im, 148, 163, 184); // #94a3b8

        imagefilledrectangle($im, 0, 0, $width, $height, $bg);

        // Elegant double frame borders
        imagesetthickness($im, 3);
        imagerectangle($im, 24, 24, $width - 24, $height - 24, $goldBorder);
        imagesetthickness($im, 1);
        imagerectangle($im, 32, 32, $width - 32, $height - 32, $gold);

        // Brand banner header
        $brandText = 'HINDI PDF LIBRARY';
        imagestring($im, 4, (int) (($width - strlen($brandText) * 8) / 2), 60, $brandText, $gold);

        // Title text rendering
        $displayTitle = mb_substr($title, 0, 80);
        $lines = explode("\n", wordwrap($displayTitle, 26, "\n"));
        $y = 320;
        foreach ($lines as $line) {
            $x = max(50, (int) (($width - strlen($line) * 9) / 2));
            imagestring($im, 5, $x, $y, $line, $white);
            $y += 30;
        }

        // Author attribution
        if ($author) {
            $authorLine = '— '.$author;
            $ax = max(50, (int) (($width - strlen($authorLine) * 9) / 2));
            imagestring($im, 4, $ax, $y + 40, $authorLine, $gold);
        }

        // Footer tag
        $footer = 'DIGITAL ARCHIVE EDITION';
        imagestring($im, 2, (int) (($width - strlen($footer) * 6) / 2), $height - 60, $footer, $slate);

        ob_start();
        imagejpeg($im, null, 90);
        $jpgData = ob_get_clean();
        imagedestroy($im);

        if ($jpgData) {
            $filename = 'covers/generated_'.$slug.'_'.Str::random(10).'.jpg';
            Storage::disk('public')->put($filename, $jpgData);

            return $filename;
        }

        return null;
    }

    /**
     * Check if a CLI binary command is executable on the system.
     */
    protected function hasBinary(string $binary): bool
    {
        $cmd = PHP_OS_FAMILY === 'Windows' ? "where {$binary}" : "which {$binary}";
        @exec($cmd, $output, $returnVar);

        return $returnVar === 0 && ! empty($output);
    }

    /**
     * Extract first page via pdftoppm CLI if installed.
     */
    protected function extractViaPdftoppm(string $pdfPath, string $slug): ?string
    {
        $tempPrefix = tempnam(sys_get_temp_dir(), 'pdf_cover_');
        $cmd = sprintf(
            'pdftoppm -jpeg -r 150 -f 1 -l 1 %s %s',
            escapeshellarg($pdfPath),
            escapeshellarg($tempPrefix)
        );

        @exec($cmd, $output, $code);

        $generated = $tempPrefix.'-1.jpg';
        if ($code === 0 && file_exists($generated)) {
            $bytes = file_get_contents($generated);
            @unlink($generated);
            @unlink($tempPrefix);

            if ($bytes) {
                $filename = 'covers/cover_'.$slug.'_'.Str::random(10).'.jpg';
                Storage::disk('public')->put($filename, $bytes);

                return $filename;
            }
        }

        @unlink($tempPrefix);

        return null;
    }
}
