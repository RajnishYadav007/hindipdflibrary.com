<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic, localized XML sitemap.
     */
    public function index(): Response
    {
        $supportedLocales = array_keys(config('localization.supported_locales', ['en' => [], 'hi' => []]));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        // 1. Localized Homepages
        foreach ($supportedLocales as $loc) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.htmlspecialchars(url("/{$loc}"), ENT_XML1, 'UTF-8')."</loc>\n";
            $xml .= '    <lastmod>'.date('Y-m-d')."</lastmod>\n";
            $xml .= "    <changefreq>daily</changefreq>\n";
            $xml .= "    <priority>1.0</priority>\n";
            foreach ($supportedLocales as $altLoc) {
                $xml .= '    <xhtml:link rel="alternate" hreflang="'.$altLoc.'" href="'.htmlspecialchars(url("/{$altLoc}"), ENT_XML1, 'UTF-8')."\" />\n";
            }
            $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="'.htmlspecialchars(url('/en'), ENT_XML1, 'UTF-8')."\" />\n";
            $xml .= "  </url>\n";
        }

        // 2. Active Categories
        Category::active()
            ->with('translations')
            ->chunk(100, function ($categories) use (&$xml, $supportedLocales) {
                foreach ($categories as $cat) {
                    $available = $cat->translations->pluck('locale')->unique()->all();
                    foreach ($available as $loc) {
                        if (! in_array($loc, $supportedLocales, true)) {
                            continue;
                        }
                        $xml .= "  <url>\n";
                        $xml .= '    <loc>'.htmlspecialchars(url("/{$loc}/category/{$cat->slug}"), ENT_XML1, 'UTF-8')."</loc>\n";
                        $xml .= '    <lastmod>'.$cat->updated_at->toDateString()."</lastmod>\n";
                        $xml .= "    <changefreq>weekly</changefreq>\n";
                        $xml .= "    <priority>0.7</priority>\n";
                        foreach ($available as $altLoc) {
                            if (in_array($altLoc, $supportedLocales, true)) {
                                $xml .= '    <xhtml:link rel="alternate" hreflang="'.$altLoc.'" href="'.htmlspecialchars(url("/{$altLoc}/category/{$cat->slug}"), ENT_XML1, 'UTF-8')."\" />\n";
                            }
                        }
                        if (in_array('en', $available, true)) {
                            $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="'.htmlspecialchars(url("/en/category/{$cat->slug}"), ENT_XML1, 'UTF-8')."\" />\n";
                        }
                        $xml .= "  </url>\n";
                    }
                }
            });

        // 3. Active Authors
        Author::active()
            ->with('translations')
            ->chunk(100, function ($authors) use (&$xml, $supportedLocales) {
                foreach ($authors as $author) {
                    $available = $author->translations->pluck('locale')->unique()->all();
                    foreach ($available as $loc) {
                        if (! in_array($loc, $supportedLocales, true)) {
                            continue;
                        }
                        $xml .= "  <url>\n";
                        $xml .= '    <loc>'.htmlspecialchars(url("/{$loc}/author/{$author->slug}"), ENT_XML1, 'UTF-8')."</loc>\n";
                        $xml .= '    <lastmod>'.$author->updated_at->toDateString()."</lastmod>\n";
                        $xml .= "    <changefreq>weekly</changefreq>\n";
                        $xml .= "    <priority>0.7</priority>\n";
                        foreach ($available as $altLoc) {
                            if (in_array($altLoc, $supportedLocales, true)) {
                                $xml .= '    <xhtml:link rel="alternate" hreflang="'.$altLoc.'" href="'.htmlspecialchars(url("/{$altLoc}/author/{$author->slug}"), ENT_XML1, 'UTF-8')."\" />\n";
                            }
                        }
                        if (in_array('en', $available, true)) {
                            $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="'.htmlspecialchars(url("/en/author/{$author->slug}"), ENT_XML1, 'UTF-8')."\" />\n";
                        }
                        $xml .= "  </url>\n";
                    }
                }
            });

        // 4. Published Books (Drafts & Archived strictly excluded)
        Book::published()
            ->with('translations')
            ->chunk(100, function ($books) use (&$xml, $supportedLocales) {
                foreach ($books as $book) {
                    $available = $book->translations->pluck('locale')->unique()->all();
                    foreach ($available as $loc) {
                        if (! in_array($loc, $supportedLocales, true)) {
                            continue;
                        }
                        $xml .= "  <url>\n";
                        $xml .= '    <loc>'.htmlspecialchars(url("/{$loc}/book/{$book->slug}"), ENT_XML1, 'UTF-8')."</loc>\n";
                        $xml .= '    <lastmod>'.$book->updated_at->toDateString()."</lastmod>\n";
                        $xml .= "    <changefreq>weekly</changefreq>\n";
                        $xml .= "    <priority>0.8</priority>\n";
                        foreach ($available as $altLoc) {
                            if (in_array($altLoc, $supportedLocales, true)) {
                                $xml .= '    <xhtml:link rel="alternate" hreflang="'.$altLoc.'" href="'.htmlspecialchars(url("/{$altLoc}/book/{$book->slug}"), ENT_XML1, 'UTF-8')."\" />\n";
                            }
                        }
                        if (in_array('en', $available, true)) {
                            $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="'.htmlspecialchars(url("/en/book/{$book->slug}"), ENT_XML1, 'UTF-8')."\" />\n";
                        }
                        $xml .= "  </url>\n";
                    }
                }
            });

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }
}
