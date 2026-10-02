<?php

namespace Tests\Feature\ContentSystems;

use App\Models\Audiobook;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AudiobookTest extends TestCase
{
    use RefreshDatabase;

    protected Language $language;

    protected function setUp(): void
    {
        parent::setUp();
        $this->language = Language::create(['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी']);
    }

    public function test_audiobook_index_and_show_pages_load_for_published_audiobooks(): void
    {
        $audiobook = Audiobook::create([
            'slug' => 'test-godan-audiobook',
            'language_id' => $this->language->id,
            'narrator' => 'समीर शर्मा',
            'duration_seconds' => 3600,
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now(),
        ]);

        $audiobook->translations()->create([
            'locale' => 'hi',
            'title' => 'गोदान ऑडियो उपन्यास',
            'description' => 'प्रेमचंद का महान उपन्यास।',
        ]);

        // Published chapter
        $ch1 = $audiobook->chapters()->create([
            'chapter_number' => 1,
            'status' => 'published',
            'duration_seconds' => 1800,
            'external_audio_url' => 'https://example.com/ch1.mp3',
        ]);
        $ch1->translations()->create(['locale' => 'hi', 'title' => 'पहला अध्याय']);

        // Unpublished (draft) chapter
        $ch2 = $audiobook->chapters()->create([
            'chapter_number' => 2,
            'status' => 'draft',
            'duration_seconds' => 1800,
            'external_audio_url' => 'https://example.com/ch2.mp3',
        ]);
        $ch2->translations()->create(['locale' => 'hi', 'title' => 'दूसरा अप्रकाशित अध्याय']);

        // Test Index
        $indexResponse = $this->get('/hi/audiobooks');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('गोदान ऑडियो उपन्यास');
        $indexResponse->assertSee('समीर शर्मा');

        // Test Show
        $showResponse = $this->get("/hi/audiobooks/{$audiobook->slug}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee('गोदान ऑडियो उपन्यास');
        $showResponse->assertSee('पहला अध्याय');

        // Unpublished chapter must NOT appear in the player playlist
        $showResponse->assertDontSee('दूसरा अप्रकाशित अध्याय');
    }

    public function test_draft_or_pending_audiobook_is_hidden_from_public(): void
    {
        $draft = Audiobook::create([
            'slug' => 'draft-audiobook-test',
            'language_id' => $this->language->id,
            'status' => 'pending_review',
            'visibility' => 'public',
        ]);

        $draft->translations()->create([
            'locale' => 'hi',
            'title' => 'समीक्षाधीन ऑडियोबुक',
        ]);

        $response = $this->get("/hi/audiobooks/{$draft->slug}");
        $response->assertStatus(404);

        $indexResponse = $this->get('/hi/audiobooks');
        $indexResponse->assertDontSee('समीक्षाधीन ऑडियोबुक');
    }
}
