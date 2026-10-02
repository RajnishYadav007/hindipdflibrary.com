<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationAndPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_books_page_loads_successfully(): void
    {
        $response = $this->get('/books');
        $response->assertStatus(200);

        $responseHi = $this->get('/hi/books');
        $responseHi->assertStatus(200);
    }

    public function test_catalog_authors_page_loads_successfully(): void
    {
        $response = $this->get('/authors');
        $response->assertStatus(200);

        $responseHi = $this->get('/hi/authors');
        $responseHi->assertStatus(200);
    }

    public function test_catalog_categories_page_loads_successfully(): void
    {
        $response = $this->get('/categories');
        $response->assertStatus(200);

        $responseHi = $this->get('/hi/categories');
        $responseHi->assertStatus(200);
    }

    public function test_catalog_genres_page_loads_successfully(): void
    {
        $response = $this->get('/genres');
        $response->assertStatus(200);

        $responseHi = $this->get('/hi/genres');
        $responseHi->assertStatus(200);
    }

    public function test_all_footer_information_and_legal_pages_load(): void
    {
        $pages = [
            '/about',
            '/help',
            '/contact',
            '/suggest-book',
            '/privacy',
            '/terms',
            '/copyright',
            '/public-domain',
            '/hi/about',
            '/hi/help',
            '/hi/contact',
            '/hi/suggest-book',
            '/hi/privacy',
            '/hi/terms',
            '/hi/copyright',
            '/hi/public-domain',
        ];

        foreach ($pages as $url) {
            $response = $this->get($url);
            $response->assertStatus(200, "Failed asserting that {$url} returns 200 OK.");
        }
    }

    public function test_contact_form_submission_validates_and_redirects(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Rahul Sharma',
            'email' => 'rahul@example.com',
            'subject' => 'Book Query',
            'message' => 'Hello, I love this library initiative.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
    }

    public function test_suggest_book_form_submission_validates_and_redirects(): void
    {
        $response = $this->post('/suggest-book', [
            'title' => 'Maila Anchal',
            'author' => 'Phanishwar Nath Renu',
            'publication_year' => 1954,
            'notes' => 'A classic regional Hindi novel.',
            'email' => 'reader@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
    }
}
