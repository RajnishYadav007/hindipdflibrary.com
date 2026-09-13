<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Books
            'books.view',
            'books.create',
            'books.update',
            'books.delete',
            'books.publish',

            // Authors
            'authors.view',
            'authors.create',
            'authors.update',
            'authors.delete',

            // Categories
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',

            // Genres
            'genres.view',
            'genres.create',
            'genres.update',
            'genres.delete',

            // Languages
            'languages.view',
            'languages.create',
            'languages.update',
            'languages.delete',

            // Reviews
            'reviews.view',
            'reviews.moderate',
            'reviews.delete',

            // Users
            'users.view',
            'users.update',
            'users.delete',
            'users.manage_roles',

            // Media
            'media.view',
            'media.upload',
            'media.update',
            'media.delete',

            // SEO
            'seo.view',
            'seo.update',

            // Redirects
            'redirects.view',
            'redirects.create',
            'redirects.update',
            'redirects.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // 1. Super Admin Role
        $superAdmin = Role::findOrCreate('Super Admin', 'web');

        // 2. Admin Role (Broad operational access)
        $admin = Role::findOrCreate('Admin', 'web');
        $admin->syncPermissions([
            'books.view', 'books.create', 'books.update', 'books.delete', 'books.publish',
            'authors.view', 'authors.create', 'authors.update', 'authors.delete',
            'categories.view', 'categories.create', 'categories.update', 'categories.delete',
            'genres.view', 'genres.create', 'genres.update', 'genres.delete',
            'languages.view', 'languages.create', 'languages.update', 'languages.delete',
            'reviews.view', 'reviews.moderate', 'reviews.delete',
            'users.view', 'users.update',
            'media.view', 'media.upload', 'media.update', 'media.delete',
            'seo.view', 'seo.update',
            'redirects.view', 'redirects.create', 'redirects.update', 'redirects.delete',
        ]);

        // 3. Editor Role (Content creation and publishing)
        $editor = Role::findOrCreate('Editor', 'web');
        $editor->syncPermissions([
            'books.view', 'books.create', 'books.update',
            'authors.view', 'authors.create', 'authors.update',
            'categories.view', 'categories.create', 'categories.update',
            'genres.view', 'genres.create', 'genres.update',
            'languages.view',
            'media.view', 'media.upload', 'media.update',
            'seo.view', 'seo.update',
            'redirects.view',
        ]);

        // 4. Moderator Role (Review moderation)
        $moderator = Role::findOrCreate('Moderator', 'web');
        $moderator->syncPermissions([
            'reviews.view', 'reviews.moderate', 'reviews.delete',
            'books.view',
            'authors.view',
        ]);
    }
}
