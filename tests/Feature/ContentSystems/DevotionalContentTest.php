<?php

namespace Tests\Feature\ContentSystems;

use App\Models\DevotionalContent;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevotionalContentTest extends TestCase
{
    use RefreshDatabase;

    protected Language $language;

    protected function setUp(): void
    {
        parent::setUp();
        $this->language = Language::create(['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी']);
    }

    public function test_chalisa_and_aarti_catalogs_and_readers_render_cleanly(): void
    {
        $chalisa = DevotionalContent::create([
            'type' => 'chalisa',
            'slug' => 'test-hanuman-chalisa',
            'language_id' => $this->language->id,
            'deity_name' => 'हनुमान',
            'author_name' => 'तुलसीदास',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $chalisa->translations()->create([
            'locale' => 'hi',
            'title' => 'श्री हनुमान चालीसा टेस्ट',
            'content' => "जय हनुमान ज्ञान गुन सागर।\nजय कपीस तिहुं लोक उजागर॥",
        ]);

        $aarti = DevotionalContent::create([
            'type' => 'aarti',
            'slug' => 'test-hanuman-aarti',
            'language_id' => $this->language->id,
            'deity_name' => 'हनुमान',
            'author_name' => 'पारम्परिक',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $aarti->translations()->create([
            'locale' => 'hi',
            'title' => 'हनुमान जी की पावन आरती टेस्ट',
            'content' => "आरती कीजै हनुमान लला की।\nदुष्ट दलन रघुनाथ कला की॥",
        ]);

        // Chalisa Index
        $chalisaIndex = $this->get('/hi/chalisa');
        $chalisaIndex->assertStatus(200);
        $chalisaIndex->assertSee('श्री हनुमान चालीसा टेस्ट');
        $chalisaIndex->assertDontSee('हनुमान जी की पावन आरती टेस्ट'); // Only chalisas

        // Aarti Index
        $aartiIndex = $this->get('/hi/aarti');
        $aartiIndex->assertStatus(200);
        $aartiIndex->assertSee('हनुमान जी की पावन आरती टेस्ट');
        $aartiIndex->assertDontSee('श्री हनुमान चालीसा टेस्ट'); // Only aartis

        // Chalisa Show with TTS
        $chalisaShow = $this->get("/hi/chalisa/{$chalisa->slug}");
        $chalisaShow->assertStatus(200);
        $chalisaShow->assertSee('जय हनुमान ज्ञान गुन सागर');
        $chalisaShow->assertSee('पाठ सुनें (Listen Audio)'); // Reusable TTS controller
    }
}
