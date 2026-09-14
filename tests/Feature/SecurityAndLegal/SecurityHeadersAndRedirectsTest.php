<?php

namespace Tests\Feature\SecurityAndLegal;

use App\Models\Redirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersAndRedirectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_present_on_web_responses(): void
    {
        $response = $this->get('/');

        $response->assertSuccessful();
        $response->assertHeader('x-content-type-options', 'nosniff');
        $response->assertHeader('x-frame-options', 'SAMEORIGIN');
        $response->assertHeader('referrer-policy', 'strict-origin-when-cross-origin');
        $this->assertTrue($response->headers->has('permissions-policy'));
    }

    public function test_active_redirect_returns_301_and_increments_hit_count(): void
    {
        $redirect = Redirect::create([
            'source_path' => '/legacy-old-book',
            'destination_path' => '/books/modern-book',
            'status_code' => 301,
            'is_active' => true,
            'hit_count' => 0,
        ]);

        $response = $this->get('/legacy-old-book');

        $response->assertStatus(301);
        $response->assertRedirect('/books/modern-book');

        $redirect->refresh();
        $this->assertEquals(1, $redirect->hit_count);
        $this->assertNotNull($redirect->last_accessed_at);
    }

    public function test_inactive_redirect_is_ignored(): void
    {
        Redirect::create([
            'source_path' => '/disabled-redirect',
            'destination_path' => '/books/target',
            'status_code' => 301,
            'is_active' => false,
            'hit_count' => 0,
        ]);

        $response = $this->get('/disabled-redirect');

        // Not redirected; returns 404 because no route exists for /disabled-redirect
        $response->assertNotFound();
    }
}
