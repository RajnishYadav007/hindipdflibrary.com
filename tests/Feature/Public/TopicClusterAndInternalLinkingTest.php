<?php

namespace Tests\Feature\Public;

use App\Enums\BookStatus;
use App\Enums\CopyrightStatus;
use App\Models\Book;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopicClusterAndInternalLinkingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Language::firstOrCreate(['code' => 'hi'], ['name' => 'Hindi', 'native_name' => 'हिन्दी', 'direction' => 'ltr']);
        Language::firstOrCreate(['code' => 'en'], ['name' => 'English', 'native_name' => 'English', 'direction' => 'ltr']);
    }

    public function test_book_with_deity_automatically_links_to_related_prayers(): void
    {
        // 1. Create a prayer (Chalisa)
        $chalisa = Book::create([
            'slug' => 'test-hanuman-chalisa',
            'content_type' => 'chalisa',
            'deity' => 'hanuman',
            'status' => BookStatus::Published->value,
            'copyright_status' => CopyrightStatus::PublicDomain->value,
        ]);
        $chalisa->translations()->create([
            'locale' => 'hi',
            'title' => 'परीक्षण हनुमान चालीसा',
            'lyrics' => 'जय हनुमान ज्ञान गुन सागर',
        ]);

        // 2. Create a holy book of the same deity
        $book = Book::create([
            'slug' => 'test-sundarkand-book',
            'content_type' => 'book',
            'deity' => 'hanuman',
            'status' => BookStatus::Published->value,
            'copyright_status' => CopyrightStatus::PublicDomain->value,
        ]);
        $book->translations()->create([
            'locale' => 'hi',
            'title' => 'परीक्षण सुंदरकांड पुस्तक',
            'description' => 'पवित्र सुंदरकांड ग्रंथ विवरण',
        ]);

        // Visit the book page
        $response = $this->get("/hi/book/{$book->slug}");
        $response->assertOk();
        $response->assertSee('संबंधित चालीसा, आरती व स्तोत्र पाठ');
        $response->assertSee('test-hanuman-chalisa');
    }

    public function test_prayer_automatically_links_to_sister_prayers_and_deity_books(): void
    {
        // 1. Chalisa
        $chalisa = Book::create([
            'slug' => 'test-cluster-chalisa',
            'content_type' => 'chalisa',
            'deity' => 'hanuman',
            'status' => BookStatus::Published->value,
            'copyright_status' => CopyrightStatus::PublicDomain->value,
        ]);
        $chalisa->translations()->create([
            'locale' => 'hi',
            'title' => 'क्लस्टर चालीसा',
            'lyrics' => 'चालीसा पाठ...',
        ]);

        // 2. Aarti
        $aarti = Book::create([
            'slug' => 'test-cluster-aarti',
            'content_type' => 'aarti',
            'deity' => 'hanuman',
            'status' => BookStatus::Published->value,
            'copyright_status' => CopyrightStatus::PublicDomain->value,
        ]);
        $aarti->translations()->create([
            'locale' => 'hi',
            'title' => 'क्लस्टर आरती',
            'lyrics' => 'आरती कीजै...',
        ]);

        // 3. Book
        $book = Book::create([
            'slug' => 'test-cluster-book',
            'content_type' => 'book',
            'deity' => 'hanuman',
            'status' => BookStatus::Published->value,
            'copyright_status' => CopyrightStatus::PublicDomain->value,
        ]);
        $book->translations()->create([
            'locale' => 'hi',
            'title' => 'क्लस्टर ग्रंथ',
            'description' => 'ग्रंथ विवरण...',
        ]);

        // Viewing Chalisa should link to Aarti and Book
        $response = $this->get("/hi/book/{$chalisa->slug}");
        $response->assertOk();
        $response->assertSee('test-cluster-aarti');
        $response->assertSee('test-cluster-book');
        $response->assertSee('संबंधित पावन ग्रंथ व पुस्तकें');
    }

    public function test_devotional_direct_shortcuts_redirect_cleanly(): void
    {
        $responseAarti = $this->get('/hanuman-aarti');
        $responseAarti->assertRedirect('/hi/book/hanuman-aarti');

        $responseBaan = $this->get('/bajrang-baan');
        $responseBaan->assertRedirect('/hi/book/bajrang-baan');
    }
}
