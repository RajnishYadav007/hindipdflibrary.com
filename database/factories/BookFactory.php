<?php

namespace Database\Factories;

use App\Enums\BookStatus;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        $title = fake()->words(3, true);

        return [
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 999999),
            'status' => BookStatus::Published,
            'publication_year' => fake()->numberBetween(1920, 2024),
            'publisher' => fake()->company().' Publications',
            'isbn' => fake()->numerify('##########'),
            'isbn13' => fake()->numerify('#############'),
            'pages' => fake()->numberBetween(64, 750),
            'edition' => 'First Edition',
            'original_language_id' => null,
            'rating_average' => 0.00,
            'rating_count' => 0,
            'review_count' => 0,
            'view_count' => fake()->numberBetween(10, 500),
            'source_url' => fake()->url(),
            'license_type' => 'Public Domain',
            'rights_holder' => 'Public Domain',
            'copyright_status' => 'public_domain',
            'permission_notes' => 'Verified cultural heritage and public domain digital edition.',
        ];
    }

    /**
     * Indicate that the book is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookStatus::Draft,
        ]);
    }
}
