<?php

namespace Tests\Feature\User;

use App\Enums\BookStatus;
use App\Models\Audiobook;
use App\Models\AudiobookChapter;
use App\Models\Book;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\DevotionalContent;
use App\Models\Language;
use App\Models\StorySeries;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected Language $language;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->language = Language::firstOrCreate(
            ['code' => 'hi'],
            ['name' => 'Hindi', 'native_name' => 'हिन्दी']
        );
        $this->category = Category::create(['slug' => 'test-cat', 'is_active' => true]);
        CategoryTranslation::create(['category_id' => $this->category->id, 'locale' => 'hi', 'name' => 'टेस्ट श्रेणी']);
    }

    public function test_guest_cannot_view_submission_detail_page(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'user_id' => $user->id,
            'category_id' => $this->category->id,
            'original_language_id' => $this->language->id,
            'status' => BookStatus::Draft->value,
            'slug' => 'test-book-guest',
            'publication_year' => 2024,
            'pages' => 100,
        ]);
        $book->translations()->create(['locale' => 'hi', 'title' => 'अतिथि परीक्षण']);

        $response = $this->get(route('dashboard.submissions.show', ['type' => 'book', 'id' => $book->id]));
        $response->assertRedirect('/login');
    }

    public function test_user_can_view_own_submission_detail_page(): void
    {
        $user = User::factory()->create(['name' => 'Rajnish Kumar', 'email' => 'private_email@example.com']);
        $book = Book::create([
            'user_id' => $user->id,
            'category_id' => $this->category->id,
            'original_language_id' => $this->language->id,
            'status' => BookStatus::Draft->value,
            'slug' => 'own-test-book',
            'publication_year' => 2024,
            'pages' => 120,
            'isbn' => '978-3-16-148410-0',
            'edition' => 'First Illustrated',
        ]);
        $book->translations()->create([
            'locale' => 'hi',
            'title' => 'मेरी पहली पुस्तक',
            'description' => 'पुस्तक का विवरण यहाँ है।',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.submissions.show', ['type' => 'book', 'id' => $book->id]));
        $response->assertStatus(200);
        $response->assertSee('मेरी पहली पुस्तक');
        $response->assertSee('978-3-16-148410-0');
        $response->assertSee('First Illustrated');
        $response->assertSee('Rajnish Kumar');
    }

    public function test_user_cannot_view_another_users_submission_detail_page(): void
    {
        $userA = User::factory()->create(['name' => 'User A']);
        $userB = User::factory()->create(['name' => 'User B']);

        $book = Book::create([
            'user_id' => $userA->id,
            'category_id' => $this->category->id,
            'original_language_id' => $this->language->id,
            'status' => BookStatus::Draft->value,
            'slug' => 'user-a-secret-book',
            'publication_year' => 2024,
            'pages' => 80,
        ]);
        $book->translations()->create(['locale' => 'hi', 'title' => 'गोपनीय पुस्तक']);

        // User B tries to access User A's submission detail
        $response = $this->actingAs($userB)->get(route('dashboard.submissions.show', ['type' => 'book', 'id' => $book->id]));
        $response->assertStatus(403);
    }

    public function test_moderation_feedback_is_hidden_during_pending_and_draft(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'user_id' => $user->id,
            'category_id' => $this->category->id,
            'original_language_id' => $this->language->id,
            'status' => BookStatus::Draft->value,
            'slug' => 'draft-book-notes',
            'publication_year' => 2024,
            'pages' => 50,
            'admin_notes' => 'Internal moderation note: check copyright docs',
        ]);
        $book->translations()->create(['locale' => 'hi', 'title' => 'ड्राफ्ट पुस्तक']);

        $response = $this->actingAs($user)->get(route('dashboard.submissions.show', ['type' => 'book', 'id' => $book->id]));
        $response->assertStatus(200);
        // Feedback callout must NOT appear when draft or pending
        $response->assertDontSee('संपादकीय समीक्षा:');
        $response->assertDontSee('Internal moderation note: check copyright docs');
    }

    public function test_moderation_feedback_is_shown_when_revision_required(): void
    {
        $user = User::factory()->create();
        $devotional = DevotionalContent::create([
            'submitted_by' => $user->id,
            'type' => 'chalisa',
            'deity_name' => 'हनुमान जी',
            'language_id' => $this->language->id,
            'status' => 'revision_required',
            'slug' => 'revision-devotional-notes',
            'admin_notes' => 'कृपया चालीसा के दोहे और चौपाइयों की वर्तनी शुद्ध करें।',
        ]);
        $devotional->translations()->create(['locale' => 'hi', 'title' => 'हनुमान चालीसा']);

        $response = $this->actingAs($user)->get(route('dashboard.submissions.show', ['type' => 'devotional', 'id' => $devotional->id]));
        $response->assertStatus(200);
        $response->assertSee('संपादकीय समीक्षा: सुधार एवं संशोधन का सुझाव');
        $response->assertSee('कृपया चालीसा के दोहे और चौपाइयों की वर्तनी शुद्ध करें।');
    }

    public function test_book_submission_validates_source_type_conditionally(): void
    {
        $user = User::factory()->create();

        // 1. External source requires valid URL
        $response = $this->actingAs($user)->post('/dashboard/books', [
            'title' => 'बाहरी स्रोत पुस्तक',
            'new_author_name' => 'लेखक महोदय',
            'category_id' => $this->category->id,
            'original_language_id' => $this->language->id,
            'publication_year' => 2020,
            'pages' => 200,
            'source_type' => 'external',
            'source_url' => '', // Missing URL
            'terms_confirm' => 'on',
        ]);

        $response->assertSessionHasErrors(['source_url']);

        // 2. External source succeeds when URL is provided
        $responseSuccess = $this->actingAs($user)->post('/dashboard/books', [
            'title' => 'मान्य बाहरी पुस्तक',
            'new_author_name' => 'लेखक महोदय',
            'category_id' => $this->category->id,
            'original_language_id' => $this->language->id,
            'publication_year' => 2020,
            'pages' => 200,
            'source_type' => 'external',
            'source_url' => 'https://archive.org/details/test-book',
            'isbn' => '978-0-123456-47-2',
            'edition' => '2nd Edition',
            'terms_confirm' => 'on',
        ]);

        $responseSuccess->assertRedirect('/dashboard?tab=books');
        $this->assertDatabaseHas('books', [
            'user_id' => $user->id,
            'source_url' => 'https://archive.org/details/test-book',
            'isbn' => '978-0-123456-47-2',
            'edition' => '2nd Edition',
        ]);
    }

    public function test_audiobook_multi_step_flow_redirects_to_chapters_when_requested(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/dashboard/submissions/audiobook', [
            'title' => 'गोदान ऑडियो ड्रामा',
            'title_en' => 'Godan Audio Drama',
            'author_name' => 'मुंशी प्रेमचंद',
            'narrator' => 'समीर गोस्वामी',
            'language_id' => $this->language->id,
            'copyright_declared' => '1',
            'add_chapters_next' => '1',
        ]);

        $audiobook = Audiobook::where('submitted_by', $user->id)->first();
        $this->assertNotNull($audiobook);
        $response->assertRedirect(route('dashboard.submissions.audiobook.chapters', $audiobook));
    }

    public function test_audiobook_chapter_management_access_control(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create(['name' => 'Audio Creator']);
        $otherUser = User::factory()->create(['name' => 'Intruder']);

        $audiobook = Audiobook::create([
            'submitted_by' => $owner->id,
            'narrator' => 'ध्वनि कलाकार',
            'author_name' => 'लेखक',
            'language_id' => $this->language->id,
            'status' => 'draft',
            'slug' => 'test-audiobook-mgmt',
        ]);
        $audiobook->translations()->create(['locale' => 'hi', 'title' => 'ऑडियो पुस्तक']);

        // 1. Other user gets 403 on chapters page
        $response = $this->actingAs($otherUser)->get(route('dashboard.submissions.audiobook.chapters', $audiobook));
        $response->assertStatus(403);

        // 2. Owner can view chapters page
        $responseOwner = $this->actingAs($owner)->get(route('dashboard.submissions.audiobook.chapters', $audiobook));
        $responseOwner->assertStatus(200);
        $responseOwner->assertSee('अध्याय एवं ट्रैक प्रबंधन');

        // 3. Owner can add an external chapter
        $storeChapterResponse = $this->actingAs($owner)->post(route('dashboard.submissions.audiobook.chapters.store', $audiobook), [
            'title' => 'अध्याय 1: प्रारंभ',
            'chapter_number' => 1,
            'external_audio_url' => 'https://example.com/audio/chapter1.mp3',
            'duration_seconds' => 300,
        ]);
        $storeChapterResponse->assertRedirect(route('dashboard.submissions.audiobook.chapters', $audiobook));

        $this->assertDatabaseHas('audiobook_chapters', [
            'audiobook_id' => $audiobook->id,
            'chapter_number' => 1,
            'duration_seconds' => 300,
        ]);

        $chapter = AudiobookChapter::where('audiobook_id', $audiobook->id)->first();

        // 4. Other user cannot delete chapter
        $deleteIntruder = $this->actingAs($otherUser)->delete(route('dashboard.submissions.audiobook.chapters.destroy', [$audiobook, $chapter]));
        $deleteIntruder->assertStatus(403);

        // 5. Owner can delete chapter
        $deleteOwner = $this->actingAs($owner)->delete(route('dashboard.submissions.audiobook.chapters.destroy', [$audiobook, $chapter]));
        $deleteOwner->assertRedirect(route('dashboard.submissions.audiobook.chapters', $audiobook));
        $this->assertDatabaseMissing('audiobook_chapters', ['id' => $chapter->id]);
    }

    public function test_cannot_delete_chapter_of_published_audiobook(): void
    {
        $owner = User::factory()->create();

        $audiobook = Audiobook::create([
            'submitted_by' => $owner->id,
            'narrator' => 'ध्वनि कलाकार',
            'author_name' => 'लेखक',
            'status' => 'published',
            'slug' => 'published-audiobook-mgmt',
        ]);
        $audiobook->translations()->create(['locale' => 'hi', 'title' => 'प्रकाशित ऑडियो']);

        $chapter = AudiobookChapter::create([
            'audiobook_id' => $audiobook->id,
            'chapter_number' => 1,
            'duration_seconds' => 180,
            'audio_url' => 'https://example.com/chapter1.mp3',
        ]);
        $chapter->translations()->create(['locale' => 'hi', 'title' => 'प्रकाशित अध्याय']);

        $response = $this->actingAs($owner)->delete(route('dashboard.submissions.audiobook.chapters.destroy', [$audiobook, $chapter]));
        $response->assertStatus(403);
        $this->assertDatabaseHas('audiobook_chapters', ['id' => $chapter->id]);
    }

    public function test_story_submission_persists_type_and_classification(): void
    {
        $user = User::factory()->create();

        // 1. Children's story with age group
        $responseKids = $this->actingAs($user)->post('/dashboard/submissions/story', [
            'series_title' => 'नटखट खरगोश और कछुआ',
            'story_type' => 'children',
            'age_group' => '6-8',
            'language_id' => $this->language->id,
            'episode_title' => 'भाग 1: दौड़ की चुनौती',
            'episode_content' => '<p>एक जंगल में एक खरगोश और एक कछुआ रहते थे।</p>',
            'narration_type' => 'tts',
            'copyright_declared' => '1',
        ]);
        $responseKids->assertRedirect('/dashboard?tab=submissions');

        $kidsStory = StorySeries::where('submitted_by', $user->id)
            ->where('story_type', 'children')
            ->first();
        $this->assertNotNull($kidsStory);
        $this->assertEquals('6-8', $kidsStory->age_group);

        // 2. General story with traditional classification
        $responseGeneral = $this->actingAs($user)->post('/dashboard/submissions/story', [
            'series_title' => 'पंचतंत्र: ब्राह्मण और बकरा',
            'story_type' => 'general',
            'classification' => 'panchatantra',
            'language_id' => $this->language->id,
            'episode_title' => 'कथा 1: धूर्त ठग',
            'episode_content' => '<p>प्राचीन काल में एक ब्राह्मण रहता था।</p>',
            'narration_type' => 'none',
            'copyright_declared' => '1',
        ]);
        $responseGeneral->assertRedirect('/dashboard?tab=submissions');

        $generalStory = StorySeries::where('submitted_by', $user->id)
            ->where('story_type', 'general')
            ->first();
        $this->assertNotNull($generalStory);
        $this->assertEquals('panchatantra', $generalStory->classification);
    }

    public function test_user_can_access_all_submission_creation_forms_without_route_collision(): void
    {
        $user = User::factory()->create();

        // Story create with type=general (user reported issue)
        $responseStoryGeneral = $this->actingAs($user)->get('/dashboard/submissions/story/create?type=general');
        $responseStoryGeneral->assertStatus(200);

        // Story create with type=children
        $responseStoryKids = $this->actingAs($user)->get('/dashboard/submissions/story/create?type=children');
        $responseStoryKids->assertStatus(200);

        // Audiobook create
        $responseAudio = $this->actingAs($user)->get('/dashboard/submissions/audiobook/create');
        $responseAudio->assertStatus(200);

        // Chalisa create
        $responseChalisa = $this->actingAs($user)->get('/dashboard/submissions/chalisa/create');
        $responseChalisa->assertStatus(200);

        // Aarti create
        $responseAarti = $this->actingAs($user)->get('/dashboard/submissions/aarti/create');
        $responseAarti->assertStatus(200);
    }
}
