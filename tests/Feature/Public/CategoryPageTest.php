<?php

namespace Tests\Feature\Public;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_category_page_loads_with_subcategories_and_published_books(): void
    {
        $parentCat = Category::create([
            'slug' => 'literature',
            'is_active' => true,
        ]);
        CategoryTranslation::create([
            'category_id' => $parentCat->id,
            'locale' => 'hi',
            'name' => 'साहित्य',
            'description' => 'हिन्दी साहित्य की समस्त विधाएँ',
        ]);

        $subCat = Category::create([
            'parent_id' => $parentCat->id,
            'slug' => 'novels',
            'is_active' => true,
        ]);
        CategoryTranslation::create([
            'category_id' => $subCat->id,
            'locale' => 'hi',
            'name' => 'उपन्यास',
        ]);

        $book = Book::create([
            'slug' => 'godan-category-test',
            'status' => BookStatus::Published,
        ]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'गोदान उपन्यास',
        ]);
        $parentCat->books()->attach($book->id);

        $response = $this->get('/categories/literature');

        $response->assertSuccessful();
        $response->assertSee('साहित्य');
        $response->assertSee('हिन्दी साहित्य की समस्त विधाएँ');
        $response->assertSee('उपन्यास');
        $response->assertSee('गोदान उपन्यास');
        $response->assertSee('1 Published Book');
    }

    public function test_inactive_category_returns_404(): void
    {
        $category = Category::create([
            'slug' => 'inactive-category-test',
            'is_active' => false,
        ]);
        CategoryTranslation::create([
            'category_id' => $category->id,
            'locale' => 'hi',
            'name' => 'अक्रिय श्रेणी',
        ]);

        $this->get('/categories/inactive-category-test')->assertNotFound();
    }

    public function test_category_page_only_shows_published_books(): void
    {
        $category = Category::create([
            'slug' => 'cat-scoping-test',
            'is_active' => true,
        ]);
        CategoryTranslation::create([
            'category_id' => $category->id,
            'locale' => 'hi',
            'name' => 'श्रेणी परीक्षण',
        ]);

        $pub = Book::create([
            'slug' => 'cat-published-book',
            'status' => BookStatus::Published,
        ]);
        BookTranslation::create([
            'book_id' => $pub->id,
            'locale' => 'hi',
            'title' => 'श्रेणी की प्रकाशित पुस्तक',
        ]);
        $category->books()->attach($pub->id);

        $draft = Book::create([
            'slug' => 'cat-draft-book',
            'status' => BookStatus::Draft,
        ]);
        BookTranslation::create([
            'book_id' => $draft->id,
            'locale' => 'hi',
            'title' => 'श्रेणी की ड्राफ्ट पुस्तक',
        ]);
        $category->books()->attach($draft->id);

        $response = $this->get('/categories/cat-scoping-test');

        $response->assertSuccessful();
        $response->assertSee('श्रेणी की प्रकाशित पुस्तक');
        $response->assertDontSee('श्रेणी की ड्राफ्ट पुस्तक');
    }

    public function test_singular_alias_route_redirects_301_to_categories_show(): void
    {
        $response = $this->get('/category/literature');

        $response->assertRedirect('/categories/literature');
        $response->assertStatus(301);
    }
}
