<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Generate dynamic production-safe robots.txt.
     */
    public function index(): Response
    {
        $sitemapUrl = url('/sitemap.xml');

        $content = <<<ROBOTS
User-agent: *
Allow: /

Disallow: /admin/
Disallow: /dashboard
Disallow: /profile
Disallow: /settings
Disallow: /login
Disallow: /register
Disallow: /forgot-password
Disallow: /reset-password
Disallow: /favorites
Disallow: /reviews
Disallow: /ratings
Disallow: /search*
Disallow: /*/search*

Sitemap: {$sitemapUrl}

ROBOTS;

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}
