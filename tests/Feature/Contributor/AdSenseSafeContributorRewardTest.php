<?php

namespace Tests\Feature\Contributor;

use App\Enums\BookStatus;
use App\Models\Audiobook;
use App\Models\AudiobookChapter;
use App\Models\Book;
use App\Models\ContributorPointsLedger;
use App\Models\DevotionalContent;
use App\Models\Language;
use App\Models\Referral;
use App\Models\StoryEpisode;
use App\Models\StorySeries;
use App\Models\User;
use App\Services\Contributor\ContributorRewardService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use LogicException;
use Tests\TestCase;

class AdSenseSafeContributorRewardTest extends TestCase
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

    /**
     * 1. Awarding points on Book publication (+100 points).
     */
    public function test_book_publication_awards_points_to_contributor(): void
    {
        $user = User::factory()->create();

        $book = Book::create([
            'user_id' => $user->id,
            'slug' => 'test-sacred-book',
            'status' => BookStatus::Draft,
            'original_language_id' => $this->language->id,
        ]);

        $this->assertEquals(0, $user->fresh()->total_points);

        // Approve and publish
        $book->update(['status' => BookStatus::Published]);

        $user = $user->fresh();
        $this->assertEquals(100, $user->total_points);
        $this->assertEquals('contributor', $user->contributor_level);

        $this->assertDatabaseHas('contributor_points_ledger', [
            'user_id' => $user->id,
            'points' => 100,
            'event_type' => 'content_published',
            'content_type' => Book::class,
            'content_id' => $book->id,
        ]);
    }

    /**
     * 2. Awarding points on Audiobook publication (+150 points).
     */
    public function test_audiobook_publication_awards_points_to_contributor(): void
    {
        $user = User::factory()->create();

        $audiobook = Audiobook::create([
            'submitted_by' => $user->id,
            'language_id' => $this->language->id,
            'slug' => 'test-audiobook-epic',
            'status' => 'pending_review',
        ]);

        $audiobook->update(['status' => 'published']);

        $user = $user->fresh();
        $this->assertEquals(150, $user->total_points);

        $this->assertDatabaseHas('contributor_points_ledger', [
            'user_id' => $user->id,
            'points' => 150,
            'event_type' => 'content_published',
            'content_type' => Audiobook::class,
            'content_id' => $audiobook->id,
        ]);
    }

    /**
     * 3. Awarding points on Devotional content (Chalisa: +50 points).
     */
    public function test_chalisa_publication_awards_points_to_contributor(): void
    {
        $user = User::factory()->create();

        $devotional = DevotionalContent::create([
            'type' => 'chalisa',
            'submitted_by' => $user->id,
            'language_id' => $this->language->id,
            'slug' => 'test-shri-chalisa',
            'status' => 'pending_review',
        ]);

        $devotional->update(['status' => 'published']);

        $user = $user->fresh();
        $this->assertEquals(50, $user->total_points);

        $this->assertDatabaseHas('contributor_points_ledger', [
            'user_id' => $user->id,
            'points' => 50,
            'event_type' => 'content_published',
            'content_type' => DevotionalContent::class,
            'content_id' => $devotional->id,
        ]);
    }

    /**
     * 4. Awarding points on Story Series publication (+75 points).
     */
    public function test_story_series_publication_awards_points_to_contributor(): void
    {
        $user = User::factory()->create();

        $story = StorySeries::create([
            'story_type' => 'general',
            'submitted_by' => $user->id,
            'language_id' => $this->language->id,
            'slug' => 'test-folk-story-series',
            'status' => 'pending_review',
        ]);

        $story->update(['status' => 'published']);

        $user = $user->fresh();
        $this->assertEquals(75, $user->total_points);

        $this->assertDatabaseHas('contributor_points_ledger', [
            'user_id' => $user->id,
            'points' => 75,
            'event_type' => 'content_published',
            'content_type' => StorySeries::class,
            'content_id' => $story->id,
        ]);
    }

    /**
     * 5. Idempotency: Duplicate publication triggers do not award duplicate points.
     */
    public function test_idempotency_prevents_duplicate_awards(): void
    {
        $user = User::factory()->create();

        $book = Book::create([
            'user_id' => $user->id,
            'slug' => 'idempotency-test-book',
            'status' => BookStatus::Draft,
            'original_language_id' => $this->language->id,
        ]);

        $book->update(['status' => BookStatus::Published]);
        // Trigger again with identical status
        $book->update(['publication_year' => 2026]);

        $service = app(ContributorRewardService::class);
        $service->awardForPublishedContent($book);
        $service->awardForPublishedContent($book);

        $this->assertEquals(100, $user->fresh()->total_points);
        $this->assertEquals(1, ContributorPointsLedger::where('user_id', $user->id)->count());
    }

    /**
     * 6. Sub-units isolation: Audiobook chapter or Story episode publication does NOT award separate parent points.
     */
    public function test_sub_units_do_not_award_separate_points(): void
    {
        $user = User::factory()->create();

        $audiobook = Audiobook::create([
            'submitted_by' => $user->id,
            'language_id' => $this->language->id,
            'slug' => 'audiobook-parent',
            'status' => 'draft',
        ]);

        $chapter = AudiobookChapter::create([
            'audiobook_id' => $audiobook->id,
            'chapter_number' => 1,
            'slug' => 'chapter-1',
            'status' => 'published',
        ]);

        $storySeries = StorySeries::create([
            'submitted_by' => $user->id,
            'language_id' => $this->language->id,
            'slug' => 'story-parent',
            'story_type' => 'general',
            'status' => 'draft',
        ]);

        $episode = StoryEpisode::create([
            'story_series_id' => $storySeries->id,
            'episode_number' => 1,
            'slug' => 'episode-1',
            'status' => 'published',
        ]);

        $service = app(ContributorRewardService::class);
        $this->assertNull($service->awardForPublishedContent($chapter));
        $this->assertNull($service->awardForPublishedContent($episode));

        $this->assertEquals(0, ContributorPointsLedger::where('user_id', $user->id)->count());
    }

    /**
     * 7. Retraction / unpublishing creates compensating negative ledger entry and reduces cached points.
     */
    public function test_unpublishing_revokes_points_with_compensating_ledger_entry(): void
    {
        $user = User::factory()->create();

        $book = Book::create([
            'user_id' => $user->id,
            'slug' => 'retraction-test-book',
            'status' => BookStatus::Published,
            'original_language_id' => $this->language->id,
        ]);

        $this->assertEquals(100, $user->fresh()->total_points);

        // Retract/unpublish
        $book->update(['status' => BookStatus::Draft]);

        $user = $user->fresh();
        $this->assertEquals(0, $user->total_points);
        $this->assertEquals('reader', $user->contributor_level);

        $this->assertDatabaseHas('contributor_points_ledger', [
            'user_id' => $user->id,
            'points' => -100,
            'event_type' => 'content_revoked',
        ]);
    }

    /**
     * 8. Re-publishing after revocation does not double-award beyond net entitlement.
     */
    public function test_republishing_after_revocation_does_not_double_award(): void
    {
        $user = User::factory()->create();

        $book = Book::create([
            'user_id' => $user->id,
            'slug' => 'republish-test-book',
            'status' => BookStatus::Draft,
            'original_language_id' => $this->language->id,
        ]);

        // 1st Publish: +100
        $book->update(['status' => BookStatus::Published]);
        $this->assertEquals(100, $user->fresh()->total_points);

        // Retract: -100
        $book->update(['status' => BookStatus::Draft]);
        $this->assertEquals(0, $user->fresh()->total_points);

        // Re-publish: Because the original award was already tracked, idempotent key prevents double award
        $book->update(['status' => BookStatus::Published]);
        $this->assertEquals(0, $user->fresh()->total_points);
    }

    /**
     * 9. Contributor levels upgrade automatically as approved contribution count increases.
     */
    public function test_contributor_level_upgrades_automatically_with_approved_counts(): void
    {
        $user = User::factory()->create();
        $this->assertEquals('reader', $user->fresh()->contributor_level);

        // 1 Book -> Contributor
        Book::create([
            'user_id' => $user->id,
            'slug' => 'level-book-1',
            'status' => BookStatus::Published,
            'original_language_id' => $this->language->id,
        ]);
        $this->assertEquals('contributor', $user->fresh()->contributor_level);

        // 4 more books (total 5) -> Regular Contributor
        for ($i = 2; $i <= 5; $i++) {
            Book::create([
                'user_id' => $user->id,
                'slug' => "level-book-{$i}",
                'status' => BookStatus::Published,
                'original_language_id' => $this->language->id,
            ]);
        }
        $this->assertEquals('regular_contributor', $user->fresh()->contributor_level);

        // 10 more books (total 15) -> Library Contributor
        for ($i = 6; $i <= 15; $i++) {
            Book::create([
                'user_id' => $user->id,
                'slug' => "level-book-{$i}",
                'status' => BookStatus::Published,
                'original_language_id' => $this->language->id,
            ]);
        }
        $this->assertEquals('library_contributor', $user->fresh()->contributor_level);
    }

    /**
     * 10. Contributor levels downgrade correctly upon revocation if approved count drops.
     */
    public function test_contributor_level_downgrades_on_retraction(): void
    {
        $user = User::factory()->create();

        $book = Book::create([
            'user_id' => $user->id,
            'slug' => 'downgrade-book',
            'status' => BookStatus::Published,
            'original_language_id' => $this->language->id,
        ]);

        $this->assertEquals('contributor', $user->fresh()->contributor_level);

        // Delete book
        $book->delete();

        $this->assertEquals('reader', $user->fresh()->contributor_level);
    }

    /**
     * 11. Append-only ledger enforcement: Attempting update() throws LogicException.
     */
    public function test_immutable_ledger_update_throws_exception(): void
    {
        $this->expectException(LogicException::class);

        $user = User::factory()->create();
        $ledger = ContributorPointsLedger::create([
            'idempotency_key' => 'test-key-1',
            'user_id' => $user->id,
            'points' => 100,
            'event_type' => 'content_published',
            'description' => 'Test',
        ]);

        $ledger->update(['points' => 500]);
    }

    /**
     * 12. Append-only ledger enforcement: Attempting delete() throws LogicException.
     */
    public function test_immutable_ledger_delete_throws_exception(): void
    {
        $this->expectException(LogicException::class);

        $user = User::factory()->create();
        $ledger = ContributorPointsLedger::create([
            'idempotency_key' => 'test-key-2',
            'user_id' => $user->id,
            'points' => 100,
            'event_type' => 'content_published',
            'description' => 'Test',
        ]);

        $ledger->delete();
    }

    /**
     * 13. Admin manual points adjustment creates ledger entry with mandatory reason.
     */
    public function test_admin_manual_adjustment_creates_auditable_entry(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $contributor = User::factory()->create();

        $service = app(ContributorRewardService::class);
        $ledger = $service->adjustPointsByAdmin(
            $contributor,
            50,
            'Editorial quality recognition for rare manuscript',
            $admin
        );

        $this->assertNotNull($ledger);
        $this->assertEquals(50, $contributor->fresh()->total_points);
        $this->assertEquals($admin->id, $ledger->created_by);
        $this->assertStringContainsString('Editorial quality recognition', $ledger->description);
    }

    /**
     * 14. Referral registration: captures referral code, links referred_by, and awards 0 points initially.
     */
    public function test_referral_registration_captures_referrer_and_awards_zero_points_initially(): void
    {
        $referrer = User::factory()->create();
        $rewardService = app(ContributorRewardService::class);
        $refCode = $rewardService->generateReferralCode($referrer);

        $component = Volt::test('pages.auth.register')
            ->set('name', 'New Contributor')
            ->set('email', 'referee@example.com')
            ->set('password', 'SecurePass123!')
            ->set('password_confirmation', 'SecurePass123!')
            ->set('ref', $refCode);

        $component->call('register');
        $component->assertRedirect(route('dashboard', absolute: false));

        $newMember = User::where('email', 'referee@example.com')->first();
        $this->assertNotNull($newMember);
        $this->assertEquals($referrer->id, $newMember->referred_by);

        // Assert pending referral created
        $this->assertDatabaseHas('referrals', [
            'referrer_id' => $referrer->id,
            'referred_id' => $newMember->id,
            'status' => 'pending',
        ]);

        // Neither user gets points simply for registration
        $this->assertEquals(0, $referrer->fresh()->total_points);
        $this->assertEquals(0, $newMember->fresh()->total_points);
    }

    /**
     * 15. Referral bonus qualified when referee's first work is approved and published (+100 points).
     */
    public function test_referral_bonus_qualified_when_referee_first_work_published(): void
    {
        $referrer = User::factory()->create();
        $refCode = app(ContributorRewardService::class)->generateReferralCode($referrer);

        $referee = User::factory()->create([
            'referred_by' => $referrer->id,
        ]);

        Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $referee->id,
            'status' => 'pending',
        ]);

        $this->assertEquals(0, $referrer->fresh()->total_points);

        // Referee submits and gets a book approved
        Book::create([
            'user_id' => $referee->id,
            'slug' => 'referee-published-book',
            'status' => BookStatus::Published,
            'original_language_id' => $this->language->id,
        ]);

        // Referrer receives +100 referral bonus!
        $this->assertEquals(100, $referrer->fresh()->total_points);

        $this->assertDatabaseHas('referrals', [
            'referrer_id' => $referrer->id,
            'referred_id' => $referee->id,
            'status' => 'qualified',
        ]);

        $this->assertDatabaseHas('contributor_points_ledger', [
            'user_id' => $referrer->id,
            'points' => 100,
            'event_type' => 'referral_bonus',
        ]);
    }

    /**
     * 16. Referral anti-abuse: self-referral does not award points.
     */
    public function test_self_referral_rejected_and_earns_no_points(): void
    {
        $user = User::factory()->create();

        // Create invalid self-referral
        $referral = Referral::create([
            'referrer_id' => $user->id,
            'referred_id' => $user->id,
            'status' => 'pending',
        ]);

        // Trigger book publication
        Book::create([
            'user_id' => $user->id,
            'slug' => 'self-referral-book',
            'status' => BookStatus::Published,
            'original_language_id' => $this->language->id,
        ]);

        // User gets 100 for the book, but zero referral bonus
        $this->assertEquals(100, $user->fresh()->total_points);
        $this->assertEquals('rejected', $referral->fresh()->status);
    }

    /**
     * 17. Referral bonus revoked if qualifying work is retracted.
     */
    public function test_referral_bonus_revoked_if_qualifying_work_is_retracted(): void
    {
        $referrer = User::factory()->create();
        $referee = User::factory()->create(['referred_by' => $referrer->id]);

        Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $referee->id,
            'status' => 'pending',
        ]);

        $book = Book::create([
            'user_id' => $referee->id,
            'slug' => 'referee-retracted-book',
            'status' => BookStatus::Published,
            'original_language_id' => $this->language->id,
        ]);

        $this->assertEquals(100, $referrer->fresh()->total_points);

        // Referee's work is retracted
        $book->update(['status' => BookStatus::Draft]);

        // Referrer's bonus is revoked
        $this->assertEquals(0, $referrer->fresh()->total_points);

        $this->assertDatabaseHas('referrals', [
            'referrer_id' => $referrer->id,
            'referred_id' => $referee->id,
            'status' => 'revoked',
        ]);

        $this->assertDatabaseHas('contributor_points_ledger', [
            'user_id' => $referrer->id,
            'points' => -100,
            'event_type' => 'referral_bonus_revoked',
        ]);
    }

    /**
     * 18. Publication failure isolation: Reward processing error does not break content publication.
     */
    public function test_publication_failure_isolation(): void
    {
        $user = User::factory()->create();

        // Mock ContributorRewardService to throw an exception
        $mockService = $this->createMock(ContributorRewardService::class);
        $mockService->method('awardForPublishedContent')
            ->willThrowException(new Exception('Simulated database deadlock in ledger'));

        $this->app->instance(ContributorRewardService::class, $mockService);

        // Publication must still save and succeed!
        $book = Book::create([
            'user_id' => $user->id,
            'slug' => 'isolation-resilience-book',
            'status' => BookStatus::Published,
            'original_language_id' => $this->language->id,
        ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'status' => BookStatus::Published,
        ]);
    }

    /**
     * 19. Reconcile points command (contributor:reconcile-points) correctly synchronizes users.
     */
    public function test_reconcile_points_artisan_command_synchronizes_caches(): void
    {
        $user = User::factory()->create(['total_points' => 0]);

        ContributorPointsLedger::create([
            'idempotency_key' => 'reconcile-test-1',
            'user_id' => $user->id,
            'points' => 100,
            'event_type' => 'content_published',
            'description' => 'Book 1',
        ]);

        ContributorPointsLedger::create([
            'idempotency_key' => 'reconcile-test-2',
            'user_id' => $user->id,
            'points' => 50,
            'event_type' => 'content_published',
            'description' => 'Chalisa 1',
        ]);

        $this->assertEquals(0, $user->fresh()->total_points);

        $this->artisan('contributor:reconcile-points', ['--user' => $user->id])
            ->assertExitCode(0);

        $this->assertEquals(150, $user->fresh()->total_points);
        $this->assertNotEmpty($user->fresh()->referral_code);
    }

    /**
     * 20. Dashboard rendering: Loads successfully with level, points, referral code, and zero AdSense ads.
     */
    public function test_dashboard_renders_contributor_rewards_and_zero_ads(): void
    {
        $user = User::factory()->create();

        Book::create([
            'user_id' => $user->id,
            'slug' => 'dashboard-display-book',
            'status' => BookStatus::Published,
            'original_language_id' => $this->language->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('100 लाइब्रेरी अंक');
        $response->assertSee('अंक एवं स्तर');
        $response->assertSee('अंक बहीखाता विवरण');
        $response->assertSee($user->fresh()->referral_code);

        // Strict AdSense Isolation Check: Zero ad slots or ad scripts on contributor dashboard
        $content = $response->getContent();
        $this->assertStringNotContainsString('adsbygoogle', $content);
        $this->assertStringNotContainsString('google_ad_client', $content);
        $this->assertStringNotContainsString('pagead2.googlesyndication.com', $content);
    }
}
