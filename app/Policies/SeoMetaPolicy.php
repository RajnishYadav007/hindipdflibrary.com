<?php

namespace App\Policies;

use App\Models\SeoMeta;
use App\Models\User;

class SeoMetaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('seo.view');
    }

    public function view(User $user, SeoMeta $seoMeta): bool
    {
        return $user->can('seo.view');
    }

    public function create(User $user): bool
    {
        return $user->can('seo.update');
    }

    public function update(User $user, SeoMeta $seoMeta): bool
    {
        return $user->can('seo.update');
    }

    public function delete(User $user, SeoMeta $seoMeta): bool
    {
        return $user->can('seo.update');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('seo.update');
    }
}
