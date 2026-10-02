<?php

namespace Tests\Feature\Public;

use App\Enums\BookStatus;
use App\Enums\CopyrightStatus;
use App\Models\Book;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HanumanChalisaCompetitorDominationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Language::firstOrCreate(['code' => 'hi'], ['name' => 'Hindi', 'native_name' => 'हिन्दी', 'direction' => 'ltr']);
        Language::firstOrCreate(['code' => 'en'], ['name' => 'English', 'native_name' => 'English', 'direction' => 'ltr']);

        $book = Book::create([
            'slug' => 'hanuman-chalisa',
            'status' => BookStatus::Published->value,
            'copyright_status' => CopyrightStatus::PublicDomain->value,
        ]);

        $book->translations()->create([
            'locale' => 'hi',
            'title' => 'श्री हनुमान चालीसा | Shree Hanuman Chalisa',
            'seo_title' => 'श्री हनुमान चालीसा | Hanuman Chalisa Lyrics in Hindi',
            'seo_description' => 'जय हनुमान ज्ञान गुन सागर। जय कपीस तिहुं लोक उजागर।। सम्पूर्ण हनुमान चालीसा पाठ अर्थ, ऑडियो व निःशुल्क पीडीएफ डाउनलोड।',
            'lyrics' => "॥ दोहा ॥\nश्री गुरु चरन सरोज रज...\n॥ चौपाई ॥\nजय हनुमान ज्ञान गुन सागर।",
        ]);
    }

    public function test_hanuman_chalisa_direct_routes_render_successfully(): void
    {
        $responseRoot = $this->get('/hanuman-chalisa');
        $responseRoot->assertOk();

        $responseHi = $this->get('/hi/hanuman-chalisa');
        $responseHi->assertOk();
        $responseHi->assertSee('श्री हनुमान चालीसा | Hanuman Chalisa Lyrics in Hindi');
        $responseHi->assertSee('जय हनुमान ज्ञान गुन सागर');
        $responseHi->assertSee('हनुमान चालीसा के सवाल-जवाब');
    }

    public function test_hanuman_chalisa_includes_full_rich_schemas_and_faqs(): void
    {
        $response = $this->get('/hi/hanuman-chalisa');
        $response->assertOk();

        $content = $response->getContent();

        $this->assertStringContainsString('FAQPage', $content);
        $this->assertStringContainsString('Article', $content);
        $this->assertStringContainsString('MusicComposition', $content);
        $this->assertStringContainsString('हनुमान चालीसा किसने लिखी?', $content);
    }
}
