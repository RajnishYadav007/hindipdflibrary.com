<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('reviews.view');
    }

    public function view(User $user, Review $review): bool
    {
        return $user->can('reviews.view');
    }

    public function create(User $user): bool
    {
        return false; // Reviews are created by frontend users, not directly in CMS
    }

    public function update(User $user, Review $review): bool
    {
        return $user->can('reviews.moderate');
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->can('reviews.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('reviews.delete');
    }
}
