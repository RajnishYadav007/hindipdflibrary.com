<?php

namespace App\Policies;

use App\Models\Language;
use App\Models\User;

class LanguagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('languages.view');
    }

    public function view(User $user, Language $language): bool
    {
        return $user->can('languages.view');
    }

    public function create(User $user): bool
    {
        return $user->can('languages.create');
    }

    public function update(User $user, Language $language): bool
    {
        return $user->can('languages.update');
    }

    public function delete(User $user, Language $language): bool
    {
        return $user->can('languages.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('languages.delete');
    }
}
