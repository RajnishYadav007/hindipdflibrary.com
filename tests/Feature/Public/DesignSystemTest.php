<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class DesignSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_with_library_design_system(): void
    {
        $response = $this->get('/');

        $response->assertSuccessful();
        $response->assertSee('Hindi PDF Library');
        $response->assertSee('Discover, Read &amp; Study Classic Hindi Literature', false);
        $response->assertSee('Featured Literature');
        $response->assertSee('Distinguished Authors');
        $response->assertSee('Explore By Category');
        $response->assertSee('Search books, authors');
    }

    public function test_search_box_renders_semantically(): void
    {
        $html = Blade::render('<x-search-box placeholder="Search the library..." />');

        $this->assertStringContainsString('role="search"', $html);
        $this->assertStringContainsString('type="search"', $html);
        $this->assertStringContainsString('name="q"', $html);
        $this->assertStringContainsString('Search the library...', $html);
        $this->assertStringContainsString('Search Library', $html);
    }

    public function test_book_card_renders_with_metadata_and_accessible_cover(): void
    {
        $html = Blade::render('<x-book-card 
            title="गोदान (Godan)" 
            author="मुंशी प्रेमचंद" 
            category="उपन्यास" 
            language="hi" 
            year="1936" 
            rating="4.8" 
            ratingCount="420" 
        />');

        $this->assertStringContainsString('गोदान (Godan)', $html);
        $this->assertStringContainsString('मुंशी प्रेमचंद', $html);
        $this->assertStringContainsString('उपन्यास', $html);
        $this->assertStringContainsString('1936', $html);
        $this->assertStringContainsString('4.8', $html);
        $this->assertStringContainsString('420', $html);
    }

    public function test_book_card_handles_empty_cover_with_fallback(): void
    {
        $html = Blade::render('<x-book-card title="Test Book Without Cover" />');

        $this->assertStringContainsString('Test Book Without Cover', $html);
        $this->assertStringContainsString('Hindi PDF Library', $html);
        $this->assertStringContainsString('PDF', $html);
    }

    public function test_author_card_renders_correctly(): void
    {
        $html = Blade::render('<x-author-card 
            name="मुंशी प्रेमचंद" 
            bookCount="42" 
            bio="उपन्यास सम्राट" 
        />');

        $this->assertStringContainsString('मुंशी प्रेमचंद', $html);
        $this->assertStringContainsString('42 Books', $html);
        $this->assertStringContainsString('उपन्यास सम्राट', $html);
    }

    public function test_category_card_renders_correctly(): void
    {
        $html = Blade::render('<x-category-card 
            name="उपन्यास (Novels)" 
            description="Classic narratives and social realism." 
            bookCount="350" 
        />');

        $this->assertStringContainsString('उपन्यास (Novels)', $html);
        $this->assertStringContainsString('Classic narratives and social realism.', $html);
        $this->assertStringContainsString('350', $html);
    }

    public function test_review_card_renders_safely(): void
    {
        $html = Blade::render('<x-review-card 
            userName="Aarav Sharma" 
            rating="5" 
            title="A Timeless Classic" 
            body="A profound masterpiece of rural Indian realism." 
            isVerified="true" 
        />');

        $this->assertStringContainsString('Aarav Sharma', $html);
        $this->assertStringContainsString('A Timeless Classic', $html);
        $this->assertStringContainsString('A profound masterpiece of rural Indian realism.', $html);
        $this->assertStringContainsString('Verified', $html);
    }

    public function test_rating_stars_handles_fractional_ratings_and_empty_state(): void
    {
        // Populated fractional rating
        $populatedHtml = Blade::render('<x-rating-stars rating="4.5" ratingCount="120" />');
        $this->assertStringContainsString('4.5', $populatedHtml);
        $this->assertStringContainsString('120', $populatedHtml);
        $this->assertStringContainsString('Rated 4.5 out of 5 stars', $populatedHtml);

        // Empty state
        $emptyHtml = Blade::render('<x-rating-stars :rating="0" />');
        $this->assertStringContainsString('No ratings yet', $emptyHtml);
    }

    public function test_favorite_button_exposes_accessible_labels(): void
    {
        // Unfavorited state
        $unfavHtml = Blade::render('<x-favorite-button :isFavorited="false" />');
        $this->assertStringContainsString('aria-label="Add to favorites"', $unfavHtml);

        // Favorited state
        $favHtml = Blade::render('<x-favorite-button :isFavorited="true" />');
        $this->assertStringContainsString('aria-label="Remove from favorites"', $favHtml);
    }

    public function test_breadcrumb_renders_semantic_navigation(): void
    {
        $items = [
            ['label' => 'Books', 'url' => '/books'],
            ['label' => 'Godan', 'url' => ''],
        ];

        $html = Blade::render('<x-breadcrumb :items="$items" />', ['items' => $items]);

        $this->assertStringContainsString('aria-label="Breadcrumb"', $html);
        $this->assertStringContainsString('<ol', $html);
        $this->assertStringContainsString('Books', $html);
        $this->assertStringContainsString('Godan', $html);
        $this->assertStringContainsString('aria-current="page"', $html);
    }

    public function test_book_metadata_omits_empty_fields(): void
    {
        $metadata = [
            'Author' => 'Munshi Premchand',
            'Publication Year' => '1936',
            'Publisher' => '', // Should be omitted
        ];

        $html = Blade::render('<x-book-metadata :metadata="$metadata" />', ['metadata' => $metadata]);

        $this->assertStringContainsString('<dl', $html);
        $this->assertStringContainsString('Munshi Premchand', $html);
        $this->assertStringContainsString('1936', $html);
        $this->assertStringNotContainsString('Publisher', $html);
    }

    public function test_related_books_renders_empty_state_when_no_books_provided(): void
    {
        $html = Blade::render('<x-related-books :books="[]" />');

        $this->assertStringContainsString('No related books available', $html);
    }

    public function test_language_switcher_renders_configured_locales(): void
    {
        $html = Blade::render('<x-language-switcher />');

        $this->assertStringContainsString('हिन्दी', $html);
        $this->assertStringContainsString('English', $html);
        $this->assertStringContainsString('संस्कृतम्', $html);
    }

    public function test_error_page_404_renders_with_design_system(): void
    {
        $response = $this->get('/non-existent-page-url-12345');

        $response->assertNotFound();
        $response->assertSee('Hindi PDF Library');
        $response->assertSee('Page Not Found');
        $response->assertSee('Back to Home');
    }
}
