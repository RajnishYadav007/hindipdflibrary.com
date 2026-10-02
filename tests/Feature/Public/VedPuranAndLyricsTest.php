<?php

namespace Tests\Feature\Public;

use App\Enums\BookStatus;
use App\Enums\CopyrightStatus;
use App\Models\Book;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VedPuranAndLyricsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Language::firstOrCreate(['code' => 'hi'], ['name' => 'Hindi', 'native_name' => 'हिन्दी', 'direction' => 'ltr']);
        Language::firstOrCreate(['code' => 'en'], ['name' => 'English', 'native_name' => 'English', 'direction' => 'ltr']);
    }

    public function test_ved_puran_hub_renders_successfully(): void
    {
        $response = $this->get('/ved-puran');
        $response->assertStatus(200);
        $response->assertSee('चारों वेद एवं १८ महापुराण');
        $response->assertSee('The 4 Vedas');
        $response->assertSee('18 Mahapuranas');
    }

    public function test_ved_puran_hub_renders_localized_route(): void
    {
        $response = $this->get('/hi/ved-puran');
        $response->assertStatus(200);
        $response->assertSee('अठारह महापुराण विवरणी');
        $response->assertSee('FAQPage');
    }

    public function test_book_with_lyrics_renders_devotional_reader_and_controls(): void
    {
        $book = Book::create([
            'slug' => 'test-chalisa',
            'status' => BookStatus::Published->value,
            'copyright_status' => CopyrightStatus::PublicDomain->value,
        ]);

        $book->translations()->create([
            'locale' => 'hi',
            'title' => 'परीक्षण चालीसा',
            'lyrics' => "॥ दोहा ॥\nजय गणेश गिरिजा सुवन।\n॥ चौपाई ॥\nजय हनुमान ज्ञान गुन सागर।",
        ]);

        $response = $this->get("/hi/book/{$book->slug}");
        $response->assertStatus(200);
        $response->assertSee('id="lyrics-section"', false);
        $response->assertSee('जय हनुमान ज्ञान गुन सागर');
        $response->assertSee('पाठ कॉपी करें');
        $response->assertDontSee('ऑडियो सुनें (Listen)');
    }
}
