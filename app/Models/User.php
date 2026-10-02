<?php

namespace App\Models;

use App\Enums\BookStatus;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'is_admin', 'total_points', 'contributor_level', 'referral_code', 'referred_by'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Determine whether the user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_admin || $this->hasAnyRole(['Super Admin', 'Admin', 'Editor', 'Moderator']);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'total_points' => 'integer',
        ];
    }

    /**
     * Reviews written by the user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Favorites saved by the user.
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Books favorited by the user.
     */
    public function favoriteBooks(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'favorites');
    }

    /**
     * Books contributed or submitted by the user.
     */
    public function submittedBooks(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Audiobooks contributed or submitted by the user.
     */
    public function submittedAudiobooks(): HasMany
    {
        return $this->hasMany(Audiobook::class, 'submitted_by');
    }

    /**
     * Devotional items (Chalisa/Aarti) submitted by the user.
     */
    public function submittedDevotionals(): HasMany
    {
        return $this->hasMany(DevotionalContent::class, 'submitted_by');
    }

    /**
     * Story series contributed or submitted by the user.
     */
    public function submittedStories(): HasMany
    {
        return $this->hasMany(StorySeries::class, 'submitted_by');
    }

    /**
     * Get the sanitized public display name of the contributor.
     * Never exposes email or internal user ID.
     */
    public function getPublicDisplayNameAttribute(): string
    {
        return htmlspecialchars(trim((string) $this->name), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Points ledger entries for this user.
     */
    public function pointsLedger(): HasMany
    {
        return $this->hasMany(ContributorPointsLedger::class);
    }

    /**
     * Referrals invited by this user.
     */
    public function referralsGiven(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Referral record for when this user was invited.
     */
    public function referralReceived(): HasOne
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    /**
     * The user who referred this user.
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Count of published contributions across all types.
     */
    public function getApprovedContributionsCountAttribute(): int
    {
        return (int) (
            $this->submittedBooks()->where('status', BookStatus::Published)->count()
            + $this->submittedAudiobooks()->where('status', 'published')->count()
            + $this->submittedDevotionals()->where('status', 'published')->count()
            + $this->submittedStories()->where('status', 'published')->count()
        );
    }

    /**
     * Configuration metadata for user's current level.
     */
    public function getLevelConfigAttribute(): array
    {
        $levels = config('contributor_rewards.levels', []);
        $levelKey = $this->contributor_level ?: 'reader';

        return $levels[$levelKey] ?? $levels['reader'] ?? [
            'key' => 'reader',
            'min_approved' => 0,
            'label' => 'पाठक (Reader)',
            'short_label' => 'पाठक',
            'icon' => 'book-open',
            'color' => 'slate',
        ];
    }

    /**
     * Configuration metadata for user's next level target.
     */
    public function getNextLevelConfigAttribute(): ?array
    {
        $levels = config('contributor_rewards.levels', []);
        $currentCount = $this->approved_contributions_count;

        foreach ($levels as $config) {
            if ($config['min_approved'] > $currentCount) {
                return $config;
            }
        }

        return null;
    }

    /**
     * Progress percentage and metrics toward the next level.
     */
    public function getProgressToNextLevelAttribute(): array
    {
        $currentCount = $this->approved_contributions_count;
        $currentConfig = $this->level_config;
        $nextConfig = $this->next_level_config;

        if (! $nextConfig) {
            return [
                'current' => $currentCount,
                'target' => $currentCount,
                'remaining' => 0,
                'percentage' => 100,
                'is_max' => true,
            ];
        }

        $minCurrent = $currentConfig['min_approved'];
        $targetNext = $nextConfig['min_approved'];
        $range = max(1, $targetNext - $minCurrent);
        $progress = max(0, $currentCount - $minCurrent);
        $percentage = min(100, (int) round(($progress / $range) * 100));

        return [
            'current' => $currentCount,
            'target' => $targetNext,
            'remaining' => max(0, $targetNext - $currentCount),
            'percentage' => $percentage,
            'is_max' => false,
        ];
    }
}
