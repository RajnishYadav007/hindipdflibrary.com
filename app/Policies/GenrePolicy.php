<?php

namespace App\Policies;

use App\Models\Genre;
use App\Models\User;

class GenrePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('genres.view');
    }

    public function view(User $user, Genre $genre): bool
    {
        return $user->can('genres.view');
    }

    public function create(User $user): bool
    {
        return $user->can('genres.create');
    }

    public function update(User $user, Genre $genre): bool
    {
        return $user->can('genres.update');
    }

    public function delete(User $user, Genre $genre): bool
    {
        return $user->can('genres.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('genres.delete');
    }

    public function restore(User $user, Genre $genre): bool
    {
        return $user->can('genres.delete');
    }

    public function forceDelete(User $user, Genre $genre): bool
    {
        return $user->can('genres.delete');
    }
}
