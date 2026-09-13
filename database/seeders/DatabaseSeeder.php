<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database for development.
     */
    public function run(): void
    {
        // Safe development user (Local only)
        User::firstOrCreate(
            ['email' => 'reader@example.com'],
            [
                'name' => 'Hindi Reader',
                'password' => bcrypt('password'),
                'is_admin' => false,
            ]
        );

        $this->call([
            LanguageSeeder::class,
            CategorySeeder::class,
            GenreSeeder::class,
            AuthorSeeder::class,
            BookSeeder::class,
        ]);
    }
}
