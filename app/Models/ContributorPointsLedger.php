<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class ContributorPointsLedger extends Model
{
    use HasFactory;

    protected $table = 'contributor_points_ledger';

    /**
     * The ledger is strictly append-only.
     * No updated_at timestamp exists.
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'idempotency_key',
        'user_id',
        'points',
        'event_type',
        'content_type',
        'content_id',
        'reference_id',
        'description',
        'created_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'content_id' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * User receiving or having points adjusted.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin user who initiated an adjustment or moderation, if applicable.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Prevent modifications to existing ledger entries to enforce immutability.
     */
    public function update(array $attributes = [], array $options = []): bool
    {
        throw new LogicException('Contributor points ledger is append-only and immutable. Entries cannot be updated.');
    }

    /**
     * Prevent deletions of ledger entries to maintain full audit trails.
     */
    public function delete(): ?bool
    {
        throw new LogicException('Contributor points ledger is append-only and immutable. Entries cannot be deleted.');
    }
}
