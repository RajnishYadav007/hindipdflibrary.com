<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CategoryTranslation>
 */
class CategoryTranslationFactory extends Factory
{
    protected $model = CategoryTranslation::class;

    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'category_id' => Category::factory(),
            'locale' => 'en',
            'name' => ucfirst($name),
            'description' => fake()->paragraph(),
            'seo_title' => ucfirst($name).' Books & Literature - Hindi PDF Library',
            'seo_description' => "Browse our curated collection of {$name} digital books and PDFs.",
        ];
    }
}
