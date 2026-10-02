<?php

namespace Tests\Feature\ContentSystems;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\BookTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookContentRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_page_strictly_shows_read_online_and_download_with_no_audio_controls(): void
    {
        $book = Book::create([
            'slug' => 'test-sacred-book',
            'status' => BookStatus::Published,
            'pages' => 250,
            'publication_year' => 1950,
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'पावन ग्रन्थ परीक्षण',
            'description' => 'यह एक पवित्र ग्रंथ है जिसका अध्ययन ज्ञानवर्धक है।',
            'lyrics' => 'दोहा: गुरु पद रज मृदु मंजुल अंजन। नयन अमिअ दृग दोष बिभंजन॥',
        ]);

        $response = $this->get("/hi/book/{$book->slug}");

        $response->assertStatus(200);

        // Primary actions MUST be present: Read Online and Download PDF
        $response->assertSee('Read Online (ऑनलाइन पढ़ें)');
        $response->assertSee('About the Book (पुस्तक परिचय)');

        // Hard business rule: Absolutely NO audio/TTS controls on book page
        $response->assertDontSee('Listen (ऑडियो सुनें)');
        $response->assertDontSee('Interactive Audio & Flip Reader');
        $response->assertDontSee('play-book-audio');
        $response->assertDontSee('window.speechSynthesis');
        $response->assertDontSee('audiobook-player');
    }
}
