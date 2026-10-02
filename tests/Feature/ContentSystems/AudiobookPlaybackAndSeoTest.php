<?php

namespace Tests\Feature\ContentSystems;

use App\Models\Audiobook;
use App\Models\Language;
use App\Models\User;
use App\Services\Archive\ArchiveOrgService;
use App\Support\Media\AudioStreamValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AudiobookPlaybackAndSeoTest extends TestCase
{
    use RefreshDatabase;

    protected Language $hindi;

    protected Language $english;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hindi = Language::create(['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी']);
        $this->english = Language::create(['code' => 'en', 'name' => 'English', 'native_name' => 'English']);
    }

    /**
     * Case A: Chapter-based audiobook loads all published chapters with direct URLs.
     */
    public function test_case_a_chapter_based_audiobook_loads_all_tracks(): void
    {
        $audiobook = Audiobook::create([
            'slug' => 'test-chapter-book',
            'language_id' => $this->hindi->id,
            'narrator' => 'परीक्षक वाचक',
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now(),
        ]);
        $audiobook->translations()->create(['locale' => 'hi', 'title' => 'अध्यायवार उपन्यास']);

        $ch1 = $audiobook->chapters()->create([
            'chapter_number' => 1,
            'status' => 'published',
            'duration_seconds' => 600,
            'external_audio_url' => 'https://cdn.example.com/audio/ch1.mp3',
        ]);
        $ch1->translations()->create(['locale' => 'hi', 'title' => 'अध्याय 1: प्रारम्भ']);

        $ch2 = $audiobook->chapters()->create([
            'chapter_number' => 2,
            'status' => 'published',
            'duration_seconds' => 900,
            'external_audio_url' => 'https://cdn.example.com/audio/ch2.mp3',
        ]);
        $ch2->translations()->create(['locale' => 'hi', 'title' => 'अध्याय 2: विकास']);

        $tracks = $audiobook->playable_tracks;
        $this->assertCount(2, $tracks);
        $this->assertEquals('https://cdn.example.com/audio/ch1.mp3', $tracks[0]['audio_url']);
        $this->assertEquals('https://cdn.example.com/audio/ch2.mp3', $tracks[1]['audio_url']);

        $response = $this->get("/hi/audiobooks/{$audiobook->slug}");
        $response->assertStatus(200);
        $response->assertSee('अध्यायवार उपन्यास');
        $response->assertSee('अध्याय 1: प्रारम्भ');
        $response->assertSeeText('अध्याय सूची (2)');
    }

    /**
     * Case B: Single-track audiobook (0 chapters, parent audio exists) synthesizes single track mode.
     */
    public function test_case_b_single_track_audiobook_synthesizes_track_from_parent(): void
    {
        $audiobook = Audiobook::create([
            'slug' => 'single-track-book',
            'language_id' => $this->hindi->id,
            'narrator' => 'एकल वाचक',
            'duration_seconds' => 3600,
            'external_audio_url' => 'https://cdn.example.com/single_track.mp3',
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now(),
        ]);
        $audiobook->translations()->create(['locale' => 'hi', 'title' => 'एकल व्याख्यान']);

        // 0 chapters in DB
        $this->assertEquals(0, $audiobook->chapters()->count());

        $tracks = $audiobook->playable_tracks;
        $this->assertCount(1, $tracks);
        $this->assertEquals('single', $tracks[0]['id']);
        $this->assertEquals('https://cdn.example.com/single_track.mp3', $tracks[0]['audio_url']);

        $response = $this->get("/hi/audiobooks/{$audiobook->slug}");
        $response->assertStatus(200);
        $response->assertSee('एकल व्याख्यान');
        $response->assertSee('एकल ट्रैक (Single Track)');
        // Must not display "अध्याय सूची (0)" button
        $response->assertDontSee('अध्याय सूची (0)');
    }

    /**
     * Case D: Audiobook with no audio source sets NO_SOURCE, disables play, and hides fake 00:00 / 00:00.
     */
    public function test_case_d_no_source_audiobook_disables_player_without_fake_time(): void
    {
        $audiobook = Audiobook::create([
            'slug' => 'no-audio-book',
            'language_id' => $this->hindi->id,
            'narrator' => 'आगामी वाचक',
            'duration_seconds' => 7200,
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now(),
        ]);
        $audiobook->translations()->create(['locale' => 'hi', 'title' => 'बिना ऑडियो उपन्यास']);

        $tracks = $audiobook->playable_tracks;
        $this->assertCount(0, $tracks);
        $this->assertFalse($audiobook->hasPlayableSource());

        $response = $this->get("/hi/audiobooks/{$audiobook->slug}");
        $response->assertStatus(200);
        $response->assertSee('इस ऑडियोबुक का ऑडियो स्रोत अभी उपलब्ध नहीं है।');
        $response->assertSee('playbackState: \'NO_SOURCE\'', false);
    }

    /**
     * Public page render must make ZERO external network requests.
     */
    public function test_public_page_render_makes_zero_external_http_requests(): void
    {
        Http::preventStrayRequests();

        $audiobook = Audiobook::create([
            'slug' => 'zero-http-request-book',
            'language_id' => $this->hindi->id,
            'narrator' => 'शांत वाचक',
            'duration_seconds' => 1800,
            'external_audio_url' => 'https://cdn.example.com/audio.mp3',
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now(),
        ]);
        $audiobook->translations()->create(['locale' => 'hi', 'title' => 'शून्य नेटवर्क अनुरोध पुस्तक']);

        $response = $this->get("/hi/audiobooks/{$audiobook->slug}");
        $response->assertStatus(200);
    }

    /**
     * Archive sync ingests all playable files into chapters with natural numeric sorting.
     */
    public function test_archive_sync_ingests_all_playable_files_into_chapters_with_numeric_sort(): void
    {
        $archiveService = app(ArchiveOrgService::class);

        $fakeMetadata = [
            'metadata' => [
                'title' => 'Test Multi Track Audiobook',
                'creator' => 'LibriVox Volunteers',
                'licenseurl' => 'http://creativecommons.org/publicdomain/mark/1.0/',
            ],
            'files' => [
                ['name' => 'book_10_author_128kb.mp3', 'track' => '10', 'length' => '1000.0', 'size' => 1000000, 'title' => '10 - Chapter 10: Conclusion'],
                ['name' => 'book_01_author_128kb.mp3', 'track' => '1', 'length' => '600.0', 'size' => 600000, 'title' => '01 - Chapter 1: Introduction'],
                ['name' => 'book_02_author_128kb.mp3', 'track' => '2', 'length' => '700.0', 'size' => 700000, 'title' => '02 - Chapter 2: The Journey'],
                ['name' => 'book_01_author_64kb.mp3', 'format' => '64Kbps MP3', 'track' => '1', 'length' => '600.0'], // 64kb derivative should be discarded
                ['name' => 'book_meta.xml', 'format' => 'Metadata'], // non-audio discarded
            ],
        ];

        Http::fake([
            'https://archive.org/metadata/test_multi_track' => Http::response($fakeMetadata, 200),
        ]);

        $audiobook = Audiobook::create([
            'slug' => 'test-sync-audiobook',
            'language_id' => $this->english->id,
            'status' => 'draft',
            'visibility' => 'public',
        ]);
        $audiobook->translations()->create(['locale' => 'en', 'title' => 'Test Sync Audiobook']);

        $syncedCount = $archiveService->syncAudiobookFromArchive($audiobook, 'test_multi_track');

        $this->assertEquals(3, $syncedCount);
        $this->assertEquals(3, $audiobook->chapters()->count());

        // Verify natural numeric ordering: Track 1, then Track 2, then Track 10
        $chapters = $audiobook->chapters()->orderBy('chapter_number')->get();
        $this->assertEquals(1, $chapters[0]->chapter_number);
        $this->assertEquals('book_01_author_128kb.mp3', $chapters[0]->source_file_name);
        $this->assertEquals(600, $chapters[0]->duration_seconds);

        $this->assertEquals(2, $chapters[1]->chapter_number);
        $this->assertEquals('book_02_author_128kb.mp3', $chapters[1]->source_file_name);
        $this->assertEquals(700, $chapters[1]->duration_seconds);

        $this->assertEquals(10, $chapters[2]->chapter_number);
        $this->assertEquals('book_10_author_128kb.mp3', $chapters[2]->source_file_name);
        $this->assertEquals(1000, $chapters[2]->duration_seconds);

        // Verify parent total duration is sum of chapters (600 + 700 + 1000 = 2300)
        $audiobook->refresh();
        $this->assertEquals(2300, $audiobook->duration_seconds);
        $this->assertEquals('Public Domain', $audiobook->copyright_status);
        $this->assertEquals('archive_org', $audiobook->source_type);
    }

    /**
     * Archive sync is idempotent: running multiple times updates records without duplicating.
     */
    public function test_archive_sync_is_idempotent_and_does_not_create_duplicate_chapters(): void
    {
        $archiveService = app(ArchiveOrgService::class);

        $fakeMetadata = [
            'metadata' => [
                'title' => 'Idempotent Sync Book',
                'licenseurl' => 'http://creativecommons.org/publicdomain/mark/1.0/',
            ],
            'files' => [
                ['name' => 'track_01.mp3', 'track' => '1', 'length' => '500.0', 'title' => 'Chapter 1: Start'],
                ['name' => 'track_02.mp3', 'track' => '2', 'length' => '600.0', 'title' => 'Chapter 2: Middle'],
            ],
        ];

        Http::fake([
            'https://archive.org/metadata/idempotent_item' => Http::response($fakeMetadata, 200),
        ]);

        $audiobook = Audiobook::create([
            'slug' => 'idempotent-book',
            'language_id' => $this->english->id,
            'status' => 'draft',
            'visibility' => 'public',
        ]);
        $audiobook->translations()->create(['locale' => 'en', 'title' => 'Idempotent Book']);

        // First run
        $archiveService->syncAudiobookFromArchive($audiobook, 'idempotent_item');
        $this->assertEquals(2, $audiobook->chapters()->count());

        // Second run
        $archiveService->syncAudiobookFromArchive($audiobook, 'idempotent_item');
        $this->assertEquals(2, $audiobook->chapters()->count());
    }

    /**
     * Archive sync does not blindly assign Public Domain when rights are unverified.
     */
    public function test_archive_sync_does_not_blindly_assign_public_domain_without_verification(): void
    {
        $archiveService = app(ArchiveOrgService::class);

        $fakeMetadata = [
            'metadata' => [
                'title' => 'Unverified Item',
                'creator' => 'Unknown Private Uploader',
                // No licenseurl or rights specified
            ],
            'files' => [
                ['name' => 'private_audio.mp3', 'track' => '1', 'length' => '300.0', 'title' => 'Chapter 1'],
            ],
        ];

        Http::fake([
            'https://archive.org/metadata/unverified_item' => Http::response($fakeMetadata, 200),
        ]);

        $audiobook = Audiobook::create([
            'slug' => 'unverified-rights-book',
            'language_id' => $this->english->id,
            'status' => 'draft',
            'visibility' => 'public',
        ]);
        $audiobook->translations()->create(['locale' => 'en', 'title' => 'Unverified Rights Book']);

        $archiveService->syncAudiobookFromArchive($audiobook, 'unverified_item');
        $audiobook->refresh();

        // Must NOT be set to Public Domain
        $this->assertNotEquals('Public Domain', $audiobook->copyright_status);
        $this->assertEquals('Pending Review', $audiobook->copyright_status);
    }

    /**
     * SSRF protection blocks loopback, private IPs, and cloud metadata endpoints.
     */
    public function test_stream_validator_blocks_ssrf_and_private_ips(): void
    {
        $this->assertFalse(AudioStreamValidator::isSafeHost('http://127.0.0.1/evil.mp3'));
        $this->assertFalse(AudioStreamValidator::isSafeHost('http://localhost/evil.mp3'));
        $this->assertFalse(AudioStreamValidator::isSafeHost('http://169.254.169.254/latest/meta-data/'));
        $this->assertFalse(AudioStreamValidator::isSafeHost('http://10.0.0.1/audio.mp3'));
        $this->assertFalse(AudioStreamValidator::isSafeHost('http://192.168.1.1/audio.mp3'));
        $this->assertFalse(AudioStreamValidator::isSafeHost('ftp://archive.org/audio.mp3'));

        // Legitimate public domain CDN is allowed
        $this->assertTrue(AudioStreamValidator::isSafeHost('https://archive.org/download/test/file.mp3'));
    }

    /**
     * Stream validator verifies Range requests and audio MIME type via active GET Range probe.
     */
    public function test_stream_validator_verifies_byte_range_support(): void
    {
        Http::fake([
            'https://ia800000.us.archive.org/*' => Http::response('ID3...', 206, [
                'Content-Type' => 'audio/mpeg',
                'Content-Range' => 'bytes 0-1023/12345678',
                'Accept-Ranges' => 'bytes',
                'Content-Length' => '1024',
            ]),
        ]);

        $result = AudioStreamValidator::validateStream('https://ia800000.us.archive.org/audio.mp3');
        $this->assertTrue($result['is_valid']);
        $this->assertTrue($result['accepts_ranges']);
        $this->assertEquals('audio/mpeg', $result['mime_type']);
        $this->assertEquals(206, $result['status']);
    }

    /**
     * Breadcrumb renders exactly ONE Home link without duplication.
     */
    public function test_breadcrumb_renders_exactly_one_home_link(): void
    {
        $audiobook = Audiobook::create([
            'slug' => 'breadcrumb-test-book',
            'language_id' => $this->hindi->id,
            'narrator' => 'परीक्षक',
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now(),
        ]);
        $audiobook->translations()->create(['locale' => 'hi', 'title' => 'गोदान ऑडियो परीक्षण']);

        $response = $this->get("/hi/audiobooks/{$audiobook->slug}");
        $response->assertStatus(200);

        // Count occurrences of Home link inside nav[aria-label="Breadcrumb"]
        $html = $response->getContent();
        preg_match('#<nav aria-label="Breadcrumb"[^>]*>(.*?)</nav>#s', $html, $matches);
        $this->assertNotEmpty($matches);
        $breadcrumbHtml = $matches[1];

        // Should contain Home only once
        $homeCount = substr_count($breadcrumbHtml, 'Home');
        $this->assertEquals(1, $homeCount, 'Breadcrumb contains duplicate Home link!');
    }

    /**
     * SEO metadata outputs canonical URL and reciprocal hreflang tags for /hi/ and /en/.
     */
    public function test_audiobook_canonical_and_reciprocal_hreflang_tags(): void
    {
        $audiobook = Audiobook::create([
            'slug' => 'seo-test-book',
            'language_id' => $this->hindi->id,
            'narrator' => 'वाचक',
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now(),
        ]);
        $audiobook->translations()->create(['locale' => 'hi', 'title' => 'एसईओ ऑडियो उपन्यास']);
        $audiobook->translations()->create(['locale' => 'en', 'title' => 'SEO Audio Novel']);

        $response = $this->get("/hi/audiobooks/{$audiobook->slug}");
        $response->assertStatus(200);

        $response->assertSee('<link rel="canonical" href="'.url("/hi/audiobooks/{$audiobook->slug}").'">', false);
        $response->assertSee('<link rel="alternate" hreflang="hi" href="'.url("/hi/audiobooks/{$audiobook->slug}").'">', false);
        $response->assertSee('<link rel="alternate" hreflang="en" href="'.url("/en/audiobooks/{$audiobook->slug}").'">', false);
        $response->assertSee('<link rel="alternate" hreflang="x-default" href="'.url("/en/audiobooks/{$audiobook->slug}").'">', false);
    }

    /**
     * Schema.org structured data strictly conforms to official vocabulary (Audiobook, readBy, hasPart).
     */
    public function test_schema_org_audiobook_validity(): void
    {
        $audiobook = Audiobook::create([
            'slug' => 'schema-test-book',
            'language_id' => $this->hindi->id,
            'narrator' => 'अमित व्यास',
            'author_name' => 'मुंशी प्रेमचंद',
            'duration_seconds' => 7200,
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now(),
        ]);
        $audiobook->translations()->create([
            'locale' => 'hi',
            'title' => 'गोदान सम्पूर्ण वाचन',
            'description' => 'प्रेमचंद का महान उपन्यास।',
        ]);

        $ch1 = $audiobook->chapters()->create([
            'chapter_number' => 1,
            'duration_seconds' => 3600,
            'external_audio_url' => 'https://example.com/part1.mp3',
            'status' => 'published',
        ]);
        $ch1->translations()->create(['locale' => 'hi', 'title' => 'अध्याय 1']);

        $response = $this->get("/hi/audiobooks/{$audiobook->slug}");
        $response->assertStatus(200);

        $response->assertSee('"@type": "Audiobook"', false);
        $response->assertSee('"name": "गोदान सम्पूर्ण वाचन"', false);
        $response->assertSee('"duration": "PT2H"', false);
        $response->assertSee('"readBy": {', false);
        $response->assertSee('"name": "अमित व्यास"', false);
        $response->assertSee('"hasPart": [', false);
        $response->assertSee('"@type": "AudioObject"', false);
        $response->assertSee('"@type": "BreadcrumbList"', false);
    }

    /**
     * Draft/pending audiobooks render noindex, nofollow for authorized admin preview and 404 for guests.
     */
    public function test_draft_audiobook_renders_noindex_nofollow(): void
    {
        $draft = Audiobook::create([
            'slug' => 'draft-hidden-book',
            'language_id' => $this->hindi->id,
            'status' => 'draft',
            'visibility' => 'public',
        ]);
        $draft->translations()->create(['locale' => 'hi', 'title' => 'अप्रकाशित ऑडियो ड्राफ्ट']);

        // Guest gets 404
        $guestResponse = $this->get("/hi/audiobooks/{$draft->slug}");
        $guestResponse->assertStatus(404);

        // Admin preview gets 200 with noindex, nofollow
        $admin = User::factory()->create(['is_admin' => true]);
        $adminResponse = $this->actingAs($admin)->get("/hi/audiobooks/{$draft->slug}");
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    /**
     * Audiobook cover image can be attached via media relation without size and correctly outputs in UI and Schema.org SEO.
     */
    public function test_audiobook_cover_image_creation_and_seo_presence(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('covers/audiobooks/test-cover.jpg', 'fake-image-bytes-for-audiobook-cover');

        $audiobook = Audiobook::create([
            'slug' => 'audiobook-with-cover',
            'language_id' => $this->hindi->id,
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now(),
        ]);
        $audiobook->translations()->create([
            'locale' => 'hi',
            'title' => 'कवर सहित ऑडियोबुक',
            'description' => 'कवर इमेज परीक्षण विवरण।',
        ]);

        // Create cover media without specifying size (tests MySQL default and auto-detection from disk)
        $media = $audiobook->media()->create([
            'collection_name' => 'cover',
            'name' => 'Cover Image',
            'file_name' => 'covers/audiobooks/test-cover.jpg',
            'disk' => 'public',
        ]);

        $this->assertNotNull($media->id);
        $this->assertGreaterThan(0, $media->size);
        $this->assertNotNull($audiobook->cover_url);
        $this->assertStringContainsString('covers/audiobooks/test-cover.jpg', $audiobook->cover_url);

        $response = $this->get("/hi/audiobooks/{$audiobook->slug}");
        $response->assertStatus(200);
        $response->assertSee($audiobook->cover_url, false);
        $response->assertSee('"image": "'.e($audiobook->cover_url).'"', false);
    }
}
