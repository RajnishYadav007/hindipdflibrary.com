<?php

namespace Tests\Feature\ContentSystems;

use App\Models\Language;
use App\Models\StorySeries;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StorySystemTest extends TestCase
{
    use RefreshDatabase;

    protected Language $language;

    protected function setUp(): void
    {
        parent::setUp();
        $this->language = Language::create(['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी']);
    }

    public function test_story_series_catalog_age_filter_and_dual_audio_modes(): void
    {
        $series = StorySeries::create([
            'slug' => 'test-stories-series',
            'language_id' => $this->language->id,
            'age_group' => '6-8',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $series->translations()->create([
            'locale' => 'hi',
            'title' => 'परी कथाएं टेस्ट श्रृंखला',
            'description' => 'रोचक बाल कहानियां।',
        ]);

        // Episode 1: TTS Mode
        $ep1 = $series->episodes()->create([
            'episode_number' => 1,
            'narration_type' => 'tts',
            'status' => 'published',
        ]);
        $ep1->translations()->create([
            'locale' => 'hi',
            'title' => 'चतुर लोमड़ी',
            'slug' => 'chatur-lomdi',
            'content' => 'एक जंगल में चतुर लोमड़ी रहती थी।',
        ]);

        // Episode 2: Uploaded Audio Mode
        $ep2 = $series->episodes()->create([
            'episode_number' => 2,
            'narration_type' => 'uploaded',
            'external_audio_url' => 'https://example.com/audio-narration.mp3',
            'status' => 'published',
        ]);
        $ep2->translations()->create([
            'locale' => 'hi',
            'title' => 'शेर और चूहा',
            'slug' => 'sher-aur-chuha',
            'content' => 'एक बार एक शेर सो रहा था।',
        ]);

        // Test Catalog
        $indexResponse = $this->get('/hi/stories');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('परी कथाएं टेस्ट श्रृंखला');

        // Test Series Detail
        $seriesResponse = $this->get("/hi/stories/{$series->slug}");
        $seriesResponse->assertStatus(200);
        $seriesResponse->assertSee('चतुर लोमड़ी');
        $seriesResponse->assertSee('शेर और चूहा');

        // Test Episode 1: TTS Mode should render TTS controller
        $ep1Response = $this->get("/hi/stories/{$series->slug}/{$ep1->slug}");
        $ep1Response->assertStatus(200);
        $ep1Response->assertSee('कहानी सुनें (Listen)'); // TTS controller

        // Test Episode 2: Uploaded mode should render native audio element
        $ep2Response = $this->get("/hi/stories/{$series->slug}/{$ep2->slug}");
        $ep2Response->assertStatus(200);
        $ep2Response->assertSee('वाचन ऑडियो:');
        $ep2Response->assertSee('https://example.com/audio-narration.mp3');
    }

    public function test_tts_stream_endpoint_validates_and_streams_audio(): void
    {
        // 1. Text required validation
        $response = $this->get('/api/tts/stream');
        $response->assertStatus(400);

        // 2. Stream with fake audio response
        Http::fake([
            'https://translate.google.com/translate_tts*' => Http::response(str_repeat('MPEG_AUDIO_STREAM_DATA_', 40), 200),
        ]);

        $streamResponse = $this->get('/api/tts/stream?text='.urlencode('एक राजा था।'));
        $streamResponse->assertStatus(200);
        $streamResponse->assertHeader('Content-Type', 'audio/mpeg');
    }
}
