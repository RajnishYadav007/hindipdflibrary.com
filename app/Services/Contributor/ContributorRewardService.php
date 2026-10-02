<?php

namespace App\Services\Contributor;

use App\Enums\BookStatus;
use App\Models\Audiobook;
use App\Models\AudiobookChapter;
use App\Models\Book;
use App\Models\ContributorPointsLedger;
use App\Models\DevotionalContent;
use App\Models\Referral;
use App\Models\StoryEpisode;
use App\Models\StorySeries;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ContributorRewardService
{
    /**
     * Award library points when a primary literature unit is approved and published.
     * Guaranteed idempotent: identical content and user will never receive duplicate rewards.
     */
    public function awardForPublishedContent(Model $content, ?User $admin = null): ?ContributorPointsLedger
    {
        // 1. Enforce publishable unit: Never award sub-units directly
        if ($content instanceof AudiobookChapter || $content instanceof StoryEpisode) {
            return null;
        }

        $userId = $this->extractContributorId($content);
        if (! $userId) {
            return null;
        }

        $user = User::find($userId);
        if (! $user) {
            return null;
        }

        $contentType = $content->getMorphClass();
        $contentId = $content->getKey();
        $points = $this->calculatePointsForContent($content);

        if ($points <= 0) {
            return null;
        }

        $idempotencyKey = hash('sha256', "award:{$contentType}:{$contentId}:{$userId}");

        return DB::transaction(function () use ($idempotencyKey, $user, $points, $contentType, $contentId, $admin) {
            // Check existing ledger entry with pessimistic lock to prevent race conditions
            $existing = ContributorPointsLedger::where('idempotency_key', $idempotencyKey)->lockForUpdate()->first();
            if ($existing) {
                return $existing;
            }

            $description = $this->generatePublicationDescription($contentType, $contentId);

            $ledger = ContributorPointsLedger::create([
                'idempotency_key' => $idempotencyKey,
                'user_id' => $user->id,
                'points' => $points,
                'event_type' => 'content_published',
                'content_type' => $contentType,
                'content_id' => $contentId,
                'reference_id' => "pub:{$contentType}:{$contentId}",
                'description' => $description,
                'created_by' => $admin?->id,
                'created_at' => now(),
            ]);

            // Reconcile contributor points and level
            $this->reconcileUserPoints($user);

            // Qualify referral if this is the user's first approved published work
            $this->qualifyReferralIfApplicable($user, $contentType, $contentId);

            return $ledger;
        });
    }

    /**
     * Revoke points when previously published literature is unpublished, rejected, or retracted.
     * Strictly idempotent and reversible.
     */
    public function revokeForUnpublishedContent(Model $content, string $reason, ?User $admin = null): ?ContributorPointsLedger
    {
        if ($content instanceof AudiobookChapter || $content instanceof StoryEpisode) {
            return null;
        }

        $userId = $this->extractContributorId($content);
        if (! $userId) {
            return null;
        }

        $user = User::find($userId);
        if (! $user) {
            return null;
        }

        $contentType = $content->getMorphClass();
        $contentId = $content->getKey();

        $awardIdempotencyKey = hash('sha256', "award:{$contentType}:{$contentId}:{$userId}");
        $revokeIdempotencyKey = hash('sha256', "revoke:{$contentType}:{$contentId}:{$userId}");

        return DB::transaction(function () use ($awardIdempotencyKey, $revokeIdempotencyKey, $user, $contentType, $contentId, $reason, $admin) {
            // Check if there was an original award
            $originalAward = ContributorPointsLedger::where('idempotency_key', $awardIdempotencyKey)->first();
            if (! $originalAward || $originalAward->points <= 0) {
                return null;
            }

            // Check if already revoked
            $existingRevocation = ContributorPointsLedger::where('idempotency_key', $revokeIdempotencyKey)->lockForUpdate()->first();
            if ($existingRevocation) {
                return $existingRevocation;
            }

            $deductedPoints = -1 * abs($originalAward->points);

            $ledger = ContributorPointsLedger::create([
                'idempotency_key' => $revokeIdempotencyKey,
                'user_id' => $user->id,
                'points' => $deductedPoints,
                'event_type' => 'content_revoked',
                'content_type' => $contentType,
                'content_id' => $contentId,
                'reference_id' => "rev:{$contentType}:{$contentId}",
                'description' => "सामग्री वापस लेने पर अंक कटौती: {$reason}",
                'created_by' => $admin?->id,
                'created_at' => now(),
            ]);

            // Reconcile contributor points and level downward
            $this->reconcileUserPoints($user);

            // If this content was a qualifying referral trigger, revoke the referrer's bonus
            $this->revokeReferralIfApplicable($user, $contentType, $contentId);

            return $ledger;
        });
    }

    /**
     * Admin manual adjustment with mandatory justification.
     */
    public function adjustPointsByAdmin(User $user, int $points, string $reason, User $admin): ContributorPointsLedger
    {
        $reason = trim($reason);
        if (empty($reason)) {
            throw new InvalidArgumentException('A clear explanation is required for manual points adjustments.');
        }

        if ($points === 0) {
            throw new InvalidArgumentException('Points adjustment cannot be zero.');
        }

        $idempotencyKey = hash('sha256', "admin_adj:{$user->id}:".uniqid('', true));

        return DB::transaction(function () use ($user, $points, $reason, $admin, $idempotencyKey) {
            $ledger = ContributorPointsLedger::create([
                'idempotency_key' => $idempotencyKey,
                'user_id' => $user->id,
                'points' => $points,
                'event_type' => 'admin_adjustment',
                'description' => "प्रशासक द्वारा अंक संशोधन: {$reason}",
                'created_by' => $admin->id,
                'created_at' => now(),
            ]);

            $this->reconcileUserPoints($user);

            return $ledger;
        });
    }

    /**
     * Recalculate and synchronize cached user points and contributor level from source of truth.
     */
    public function reconcileUserPoints(User $user): array
    {
        // 1. Total points from append-only ledger sum (floored at 0)
        $totalPoints = (int) ContributorPointsLedger::where('user_id', $user->id)->sum('points');
        $totalPoints = max(0, $totalPoints);

        // 2. Determine contributor level from approved published items count
        $approvedCount = $user->approved_contributions_count;
        $levels = config('contributor_rewards.levels', []);

        // Sort levels by min_approved descending
        uasort($levels, fn ($a, $b) => ($b['min_approved'] ?? 0) <=> ($a['min_approved'] ?? 0));

        $assignedLevel = 'reader';
        foreach ($levels as $key => $conf) {
            if ($approvedCount >= ($conf['min_approved'] ?? 0)) {
                $assignedLevel = $key;
                break;
            }
        }

        // 3. Ensure referral code exists
        $referralCode = $user->referral_code;
        if (empty($referralCode)) {
            $referralCode = $this->generateReferralCode($user);
        }

        $user->forceFill([
            'total_points' => $totalPoints,
            'contributor_level' => $assignedLevel,
            'referral_code' => $referralCode,
        ])->saveQuietly();

        return [
            'total_points' => $totalPoints,
            'contributor_level' => $assignedLevel,
            'approved_count' => $approvedCount,
        ];
    }

    /**
     * Qualify referral bonus when a referred contributor has their first published literature.
     */
    protected function qualifyReferralIfApplicable(User $user, string $contentType, int $contentId): void
    {
        if (! config('contributor_rewards.referrals.enabled', true)) {
            return;
        }

        $referral = Referral::where('referred_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (! $referral) {
            return;
        }

        $referrer = User::find($referral->referrer_id);
        if (! $referrer || $referrer->id === $user->id) {
            // Anti-abuse: do not reward self-referrals
            $referral->update(['status' => 'rejected']);

            return;
        }

        $referralPoints = (int) config('contributor_rewards.referrals.points', 100);
        $referralKey = hash('sha256', "referral:{$referrer->id}:{$user->id}");

        // Mark referral qualified
        $referral->update([
            'status' => 'qualified',
            'qualified_at' => now(),
            'qualifying_content_type' => $contentType,
            'qualifying_content_id' => $contentId,
        ]);

        // Award points to referrer
        $existing = ContributorPointsLedger::where('idempotency_key', $referralKey)->first();
        if (! $existing) {
            ContributorPointsLedger::create([
                'idempotency_key' => $referralKey,
                'user_id' => $referrer->id,
                'points' => $referralPoints,
                'event_type' => 'referral_bonus',
                'reference_id' => "referral:{$referral->id}",
                'description' => "रेफरल प्रोत्साहन: {$user->public_display_name} द्वारा प्रथम साहित्य प्रकाशन",
                'created_at' => now(),
            ]);

            $this->reconcileUserPoints($referrer);
        }
    }

    /**
     * Revoke referral bonus if the qualifying content was retracted/rejected.
     */
    protected function revokeReferralIfApplicable(User $user, string $contentType, int $contentId): void
    {
        $referral = Referral::where('referred_id', $user->id)
            ->where('status', 'qualified')
            ->where('qualifying_content_type', $contentType)
            ->where('qualifying_content_id', $contentId)
            ->first();

        if (! $referral) {
            return;
        }

        $referrer = User::find($referral->referrer_id);
        $referral->update(['status' => 'revoked']);

        if (! $referrer) {
            return;
        }

        $referralPoints = (int) config('contributor_rewards.referrals.points', 100);
        $revokeKey = hash('sha256', "revoke_referral:{$referrer->id}:{$user->id}");

        $existingRevocation = ContributorPointsLedger::where('idempotency_key', $revokeKey)->first();
        if (! $existingRevocation) {
            ContributorPointsLedger::create([
                'idempotency_key' => $revokeKey,
                'user_id' => $referrer->id,
                'points' => -1 * abs($referralPoints),
                'event_type' => 'referral_bonus_revoked',
                'reference_id' => "rev_referral:{$referral->id}",
                'description' => 'रेफरल प्रोत्साहन कटौती: संदर्भित साहित्य अस्वीकृत/वापस लिया गया',
                'created_at' => now(),
            ]);

            $this->reconcileUserPoints($referrer);
        }
    }

    /**
     * Generate or fetch user's unique referral code.
     */
    public function generateReferralCode(User $user): string
    {
        if (! empty($user->referral_code)) {
            return $user->referral_code;
        }

        do {
            $code = 'HPL'.strtoupper(Str::random(5));
        } while (User::where('referral_code', $code)->exists());

        $user->forceFill(['referral_code' => $code])->saveQuietly();

        return $code;
    }

    /**
     * Complete contributor statistics for dashboard rendering.
     * Strictly free of any AdSense, ad click, or ad impression metrics.
     */
    public function getContributorStats(User $user): array
    {
        // 1. Count totals
        $publishedBooks = $user->submittedBooks()->where('status', BookStatus::Published)->count();
        $publishedAudiobooks = $user->submittedAudiobooks()->where('status', 'published')->count();
        $publishedDevotionals = $user->submittedDevotionals()->where('status', 'published')->count();
        $publishedStories = $user->submittedStories()->where('status', 'published')->count();
        $approvedTotal = $publishedBooks + $publishedAudiobooks + $publishedDevotionals + $publishedStories;

        $pendingBooks = $user->submittedBooks()->whereIn('status', [BookStatus::Draft, 'draft', 'pending_review'])->count();
        $pendingAudiobooks = $user->submittedAudiobooks()->where('status', 'pending_review')->count();
        $pendingDevotionals = $user->submittedDevotionals()->where('status', 'pending_review')->count();
        $pendingStories = $user->submittedStories()->where('status', 'pending_review')->count();
        $pendingTotal = $pendingBooks + $pendingAudiobooks + $pendingDevotionals + $pendingStories;

        // 2. Legitimate reader engagement impact (reads, plays, listens)
        $bookReads = (int) $user->submittedBooks()->where('status', BookStatus::Published)->sum('view_count');
        $audiobookPlays = (int) $user->submittedAudiobooks()->where('status', 'published')->sum('play_count');
        $devotionalListens = (int) $user->submittedDevotionals()->where('status', 'published')->sum('view_count');
        $storyReads = (int) $user->submittedStories()->where('status', 'published')->sum('view_count');
        $totalReaderReach = $bookReads + $audiobookPlays + $devotionalListens + $storyReads;

        // 3. Referral metrics
        $referrals = Referral::where('referrer_id', $user->id)->get();
        $referralStats = [
            'total_invited' => $referrals->count(),
            'qualified' => $referrals->where('status', 'qualified')->count(),
            'pending' => $referrals->where('status', 'pending')->count(),
            'points_earned' => (int) ContributorPointsLedger::where('user_id', $user->id)
                ->where('event_type', 'referral_bonus')
                ->sum('points'),
        ];

        return [
            'total_points' => $user->total_points,
            'contributor_level' => $user->contributor_level,
            'level_config' => $user->level_config,
            'next_level_config' => $user->next_level_config,
            'progress' => $user->progress_to_next_level,
            'approved_count' => $approvedTotal,
            'pending_count' => $pendingTotal,
            'reader_reach' => $totalReaderReach,
            'breakdown' => [
                'books' => ['approved' => $publishedBooks, 'views' => $bookReads],
                'audiobooks' => ['approved' => $publishedAudiobooks, 'plays' => $audiobookPlays],
                'devotionals' => ['approved' => $publishedDevotionals, 'views' => $devotionalListens],
                'stories' => ['approved' => $publishedStories, 'views' => $storyReads],
            ],
            'referral_code' => $user->referral_code ?: $this->generateReferralCode($user),
            'referral_stats' => $referralStats,
        ];
    }

    /**
     * Extract contributor ID across different content models.
     */
    protected function extractContributorId(Model $content): ?int
    {
        if ($content instanceof Book) {
            return $content->user_id;
        }

        return $content->submitted_by ?? null;
    }

    /**
     * Determine points value based on content type and configuration.
     */
    protected function calculatePointsForContent(Model $content): int
    {
        $pointsConfig = config('contributor_rewards.points', []);

        if ($content instanceof Book) {
            return (int) ($pointsConfig['book'] ?? 100);
        }

        if ($content instanceof Audiobook) {
            return (int) ($pointsConfig['audiobook'] ?? 150);
        }

        if ($content instanceof DevotionalContent) {
            $type = $content->type ?: 'chalisa';

            return (int) ($pointsConfig[$type] ?? 50);
        }

        if ($content instanceof StorySeries) {
            $type = $content->story_type === 'children' ? 'story_children' : 'story_general';

            return (int) ($pointsConfig[$type] ?? 75);
        }

        return 0;
    }

    /**
     * Human-readable description for publication award.
     */
    protected function generatePublicationDescription(string $contentType, int $contentId): string
    {
        $typeName = match ($contentType) {
            Book::class => 'पुस्तक (Book)',
            Audiobook::class => 'ऑडियोबुक (Audiobook)',
            DevotionalContent::class => 'भक्ति साहित्य (Devotional)',
            StorySeries::class => 'कहानी श्रृंखला (Story Series)',
            default => 'साहित्य',
        };

        return "साहित्य प्रकाशन स्वीकृति: {$typeName} #{$contentId}";
    }
}
