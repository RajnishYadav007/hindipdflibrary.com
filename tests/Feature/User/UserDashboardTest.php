<?php

namespace Tests\Feature\User;

use App\Enums\BookStatus;
use App\Models\Audiobook;
use App\Models\Author;
use App\Models\AuthorTranslation;
use App\Models\Book;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\DevotionalContent;
use App\Models\Language;
use App\Models\StorySeries;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected Language $language;

    protected function setUp(): void
    {
        parent::setUp();
        $this->language = Language::firstOrCreate(
            ['code' => 'hi'],
            ['name' => 'Hindi', 'native_name' => 'हिन्दी']
        );
    }

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_dashboard_with_kpis_and_contributor_badge(): void
    {
        $user = User::factory()->create(['name' => 'Prem Reader']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Prem Reader');
        $response->assertSee('डिजिटल लाइब्रेरी योगदान मंच');
        $response->assertSee('मेरी प्रस्तुतियां');
        $response->assertSee('पसंदीदा साहित्य');
        $response->assertSee('योगदानकर्ता पहचान');
        $response->assertSee('कुल प्रस्तुतियां');
    }

    public function test_dashboard_scopes_submissions_strictly_to_authenticated_user(): void
    {
        $userA = User::factory()->create(['name' => 'User A']);
        $userB = User::factory()->create(['name' => 'User B']);

        // User A's submissions
        $bookA = Book::create([
            'user_id' => $userA->id,
            'slug' => 'user-a-book',
            'status' => BookStatus::Draft,
        ]);
        $bookA->translations()->create(['locale' => 'hi', 'title' => 'User A Book Title']);

        $audiobookA = Audiobook::create([
            'submitted_by' => $userA->id,
            'slug' => 'user-a-audiobook',
            'language_id' => $this->language->id,
            'status' => 'pending_review',
        ]);
        $audiobookA->translations()->create(['locale' => 'hi', 'title' => 'User A Audiobook Title']);

        // User B's submissions
        $bookB = Book::create([
            'user_id' => $userB->id,
            'slug' => 'user-b-book',
            'status' => BookStatus::Draft,
        ]);
        $bookB->translations()->create(['locale' => 'hi', 'title' => 'User B Secret Book']);

        $audiobookB = Audiobook::create([
            'submitted_by' => $userB->id,
            'slug' => 'user-b-audiobook',
            'language_id' => $this->language->id,
            'status' => 'pending_review',
        ]);
        $audiobookB->translations()->create(['locale' => 'hi', 'title' => 'User B Secret Audio']);

        $response = $this->actingAs($userA)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('User A Book Title');
        $response->assertSee('User A Audiobook Title');
        $response->assertDontSee('User B Secret Book');
        $response->assertDontSee('User B Secret Audio');
    }

    public function test_user_can_view_add_book_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard/books/create');
        $response->assertStatus(200);
        $response->assertSee('Add a New Book');
        $response->assertSee('पुस्तक का शीर्षक');
        $response->assertSee('लेखक की जानकारी');
    }

    public function test_user_can_submit_a_new_book_with_new_author(): void
    {
        $user = User::factory()->create(['name' => 'Contributor']);
        $category = Category::create(['slug' => 'fiction', 'is_active' => true]);
        CategoryTranslation::create(['category_id' => $category->id, 'locale' => 'hi', 'name' => 'उपन्यास']);

        $response = $this->actingAs($user)->post('/dashboard/books', [
            'title' => 'निर्मला',
            'title_en' => 'Nirmala',
            'new_author_name' => 'मुंशी प्रेमचंद',
            'category_id' => $category->id,
            'original_language_id' => $this->language->id,
            'publication_year' => 1927,
            'pages' => 250,
            'description' => 'एक प्रसिद्ध यथार्थवादी सामाजिक उपन्यास।',
            'terms_confirm' => 'on',
        ]);

        $response->assertRedirect('/dashboard?tab=books');
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('books', [
            'user_id' => $user->id,
            'publication_year' => 1927,
            'status' => BookStatus::Draft->value,
        ]);

        $book = Book::where('user_id', $user->id)->first();
        $this->assertNotNull($book);
        $this->assertCount(1, $book->authors);
        $this->assertEquals('मुंशी प्रेमचंद', $book->authors->first()->name);
    }

    public function test_user_can_submit_book_with_pdf_upload(): void
    {
        Storage::fake('private_pdfs');

        $user = User::factory()->create();
        $author = Author::create(['slug' => 'kabir', 'is_active' => true]);
        AuthorTranslation::create(['author_id' => $author->id, 'locale' => 'hi', 'name' => 'कबीरदास']);

        $pdfFile = UploadedFile::fake()->create('kabir-sakhi.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($user)->post('/dashboard/books', [
            'title' => 'कबीर साखी',
            'author_id' => $author->id,
            'publication_year' => 1950,
            'pdf_file' => $pdfFile,
            'terms_confirm' => 'on',
        ]);

        $response->assertRedirect('/dashboard?tab=books');

        $book = Book::where('user_id', $user->id)->first();
        $this->assertNotNull($book);
        $this->assertCount(1, $book->media);

        $media = $book->media->first();
        $this->assertEquals('private_pdfs', $media->disk);
        Storage::disk('private_pdfs')->assertExists($media->file_name);
    }

    public function test_user_can_delete_their_pending_draft_book(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'user_id' => $user->id,
            'slug' => 'test-draft-book',
            'status' => BookStatus::Draft,
        ]);

        $response = $this->actingAs($user)->delete("/dashboard/books/{$book->id}");
        $response->assertRedirect('/dashboard?tab=submissions');

        $this->assertSoftDeleted('books', ['id' => $book->id]);
    }

    public function test_user_cannot_delete_published_book(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'user_id' => $user->id,
            'slug' => 'test-published-book',
            'status' => BookStatus::Published,
        ]);

        $response = $this->actingAs($user)->delete("/dashboard/books/{$book->id}");
        $response->assertStatus(403);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'deleted_at' => null]);
    }

    public function test_user_cannot_delete_another_users_book(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $book = Book::create([
            'user_id' => $owner->id,
            'slug' => 'owner-book',
            'status' => BookStatus::Draft,
        ]);

        $response = $this->actingAs($otherUser)->delete("/dashboard/books/{$book->id}");
        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_pending_audiobook(): void
    {
        $user = User::factory()->create();
        $audiobook = Audiobook::create([
            'submitted_by' => $user->id,
            'slug' => 'pending-audiobook',
            'language_id' => $this->language->id,
            'status' => 'pending_review',
        ]);

        $response = $this->actingAs($user)->delete("/dashboard/submissions/audiobook/{$audiobook->id}");
        $response->assertRedirect('/dashboard?tab=submissions');

        $this->assertSoftDeleted('audiobooks', ['id' => $audiobook->id]);
    }

    public function test_user_cannot_delete_published_audiobook(): void
    {
        $user = User::factory()->create();
        $audiobook = Audiobook::create([
            'submitted_by' => $user->id,
            'slug' => 'published-audiobook',
            'language_id' => $this->language->id,
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->delete("/dashboard/submissions/audiobook/{$audiobook->id}");
        $response->assertStatus(403);
        $this->assertDatabaseHas('audiobooks', ['id' => $audiobook->id, 'deleted_at' => null]);
    }

    public function test_user_cannot_delete_another_users_audiobook(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $audiobook = Audiobook::create([
            'submitted_by' => $owner->id,
            'slug' => 'owner-audiobook',
            'language_id' => $this->language->id,
            'status' => 'pending_review',
        ]);

        $response = $this->actingAs($otherUser)->delete("/dashboard/submissions/audiobook/{$audiobook->id}");
        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_pending_devotional(): void
    {
        $user = User::factory()->create();
        $devotional = DevotionalContent::create([
            'submitted_by' => $user->id,
            'type' => 'chalisa',
            'slug' => 'pending-chalisa',
            'language_id' => $this->language->id,
            'status' => 'pending_review',
        ]);

        $response = $this->actingAs($user)->delete("/dashboard/submissions/devotional/{$devotional->id}");
        $response->assertRedirect('/dashboard?tab=submissions');

        $this->assertSoftDeleted('devotional_contents', ['id' => $devotional->id]);
    }

    public function test_user_cannot_delete_published_devotional(): void
    {
        $user = User::factory()->create();
        $devotional = DevotionalContent::create([
            'submitted_by' => $user->id,
            'type' => 'aarti',
            'slug' => 'published-aarti',
            'language_id' => $this->language->id,
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->delete("/dashboard/submissions/devotional/{$devotional->id}");
        $response->assertStatus(403);
        $this->assertDatabaseHas('devotional_contents', ['id' => $devotional->id, 'deleted_at' => null]);
    }

    public function test_user_cannot_delete_another_users_devotional(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $devotional = DevotionalContent::create([
            'submitted_by' => $owner->id,
            'type' => 'chalisa',
            'slug' => 'owner-chalisa',
            'language_id' => $this->language->id,
            'status' => 'pending_review',
        ]);

        $response = $this->actingAs($otherUser)->delete("/dashboard/submissions/devotional/{$devotional->id}");
        $response->assertStatus(403);
    }

    public function test_user_can_submit_story_and_delete_pending_story(): void
    {
        $user = User::factory()->create();

        // Submit children story
        $response = $this->actingAs($user)->post('/dashboard/submissions/story', [
            'series_title' => 'पंचतंत्र बाल कहानी',
            'story_type' => 'children',
            'age_group' => '6-8',
            'language_id' => $this->language->id,
            'description' => 'एक ज्ञानवर्धक बाल कथा।',
            'episode_title' => 'भाग 1 - बुद्धिमान खरगोश',
            'episode_content' => 'एक जंगल में एक शेर रहता था...',
            'narration_type' => 'tts',
            'copyright_declared' => '1',
        ]);

        $response->assertRedirect('/dashboard?tab=submissions');
        $this->assertDatabaseHas('story_series', [
            'submitted_by' => $user->id,
            'status' => 'pending_review',
        ]);

        $story = StorySeries::where('submitted_by', $user->id)->first();
        $this->assertNotNull($story);
        $this->assertCount(1, $story->episodes);

        // Can delete own pending story
        $delResponse = $this->actingAs($user)->delete("/dashboard/submissions/story/{$story->id}");
        $delResponse->assertRedirect('/dashboard?tab=submissions');
        $this->assertSoftDeleted('story_series', ['id' => $story->id]);
    }

    public function test_user_cannot_delete_published_story_or_another_users_story(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $publishedStory = StorySeries::create([
            'submitted_by' => $owner->id,
            'slug' => 'published-story',
            'language_id' => $this->language->id,
            'status' => 'published',
        ]);

        // Attempt by other user -> 403
        $resp1 = $this->actingAs($otherUser)->delete("/dashboard/submissions/story/{$publishedStory->id}");
        $resp1->assertStatus(403);

        // Attempt by owner on published item -> 403
        $resp2 = $this->actingAs($owner)->delete("/dashboard/submissions/story/{$publishedStory->id}");
        $resp2->assertStatus(403);
    }

    public function test_public_pages_display_contributor_badge_when_published(): void
    {
        $contributor = User::factory()->create(['name' => 'आलोक कुमार']);

        $book = Book::create([
            'user_id' => $contributor->id,
            'slug' => 'public-contributed-book',
            'status' => BookStatus::Published,
            'published_at' => now(),
        ]);
        $book->translations()->create([
            'locale' => 'hi',
            'title' => 'सार्वजनिक पुस्तक',
            'description' => 'विवरण',
        ]);

        $response = $this->get("/books/{$book->slug}");
        $response->assertStatus(200);
        $response->assertSee('योगदानकर्ता');
        $response->assertSee('आलोक कुमार');
        // Must NOT expose private user information (e.g. email)
        $response->assertDontSee($contributor->email);
    }

    public function test_user_can_toggle_and_remove_favorites(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'slug' => 'favorite-sample-book',
            'status' => BookStatus::Published,
        ]);

        // Toggle on
        $response = $this->actingAs($user)->post("/favorites/{$book->id}/toggle");
        $response->assertRedirect();
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        // Remove from dashboard
        $response = $this->actingAs($user)->delete("/dashboard/favorites/{$book->id}");
        $response->assertRedirect('/dashboard?tab=favorites');
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }
}
