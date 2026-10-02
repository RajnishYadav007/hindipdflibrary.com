<?php

namespace Tests\Feature\Public;

use App\Enums\BookStatus;
use App\Models\Author;
use App\Models\AuthorTranslation;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Language;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AudioAndCoverTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_book_with_cover_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $language = Language::create(['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी']);
        $category = Category::create(['slug' => 'kahaniyan', 'is_active' => true]);
        CategoryTranslation::create(['category_id' => $category->id, 'locale' => 'hi', 'name' => 'कहानियां']);

        $coverImage = UploadedFile::fake()->image('godan_cover.webp', 600, 900);

        $response = $this->actingAs($user)->post('/dashboard/books', [
            'title' => 'ईदगाह',
            'title_en' => 'Idgah',
            'new_author_name' => 'मुंशी प्रेमचंद',
            'category_id' => $category->id,
            'original_language_id' => $language->id,
            'publication_year' => 1933,
            'description' => 'हामिद और उसके चिमटे की अमर कहानी।',
            'cover_image' => $coverImage,
            'terms_confirm' => 'on',
        ]);

        $response->assertRedirect('/dashboard?tab=books');

        $book = Book::where('user_id', $user->id)->first();
        $this->assertNotNull($book);

        // Verify cover media was created in public disk under 'cover' collection
        $coverMedia = $book->media()->where('collection_name', 'cover')->first();
        $this->assertNotNull($coverMedia);
        $this->assertEquals('public', $coverMedia->disk);
        Storage::disk('public')->assertExists($coverMedia->file_name);

        // Verify cover_url accessor works
        $this->assertNotNull($book->cover_url);
        $this->assertStringContainsString('/storage/covers/', $book->cover_url);
    }

    public function test_book_show_page_includes_read_and_download_without_audio_controls(): void
    {
        $book = Book::create([
            'slug' => 'rashmirathi-test',
            'status' => BookStatus::Published,
            'publication_year' => 1952,
            'pages' => 140,
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'रश्मिरथी',
            'description' => 'रामधारी सिंह दिनकर द्वारा रचित कर्ण के चरित्र पर आधारित खंडकाव्य।',
            'summary' => 'रश्मिरथी दिनकर जी की सबसे लोकप्रिय रचनाओं में से एक है।',
        ]);

        $author = Author::create(['slug' => 'dinkar', 'is_active' => true]);
        AuthorTranslation::create(['author_id' => $author->id, 'locale' => 'hi', 'name' => 'रामधारी सिंह दिनकर']);
        $book->authors()->attach($author->id);

        $response = $this->get("/hi/book/{$book->slug}");

        $response->assertSuccessful();
        $response->assertSee('Read Online (ऑनलाइन पढ़ें)');
        $response->assertDontSee('play-book-audio');
        $response->assertDontSee('global-audio-player');
    }

    public function test_homepage_includes_audio_player_and_book_drawer(): void
    {
        $response = $this->get('/');

        $response->assertSuccessful();
        $response->assertSee('global-audio-player');
        $response->assertSee('book-drawer');
        $response->assertSee('Listen to Books');
    }
}
