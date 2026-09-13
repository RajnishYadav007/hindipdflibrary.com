<?php

namespace App\Services\SEO;

use App\Enums\ReviewStatus;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Support\SEO\SeoData;
use Illuminate\Support\Str;

class SeoManager
{
    /**
     * Build SEO metadata and structured data for the localized homepage.
     */
    public static function forHome(string $locale): SeoData
    {
        $siteName = config('app.name', 'Hindi PDF Library');

        $isHindi = $locale === 'hi';
        $title = $isHindi
            ? "{$siteName} — निःशुल्क हिन्दी पुस्तकें और साहित्य ऑनलाइन पढ़ें व डाउनलोड करें"
            : "{$siteName} — Free Hindi Books, Novels & Literature in PDF";

        $description = $isHindi
            ? 'हजारों निःशुल्क हिन्दी पुस्तकें, उपन्यास, धार्मिक ग्रन्थ, बाल साहित्य और कहानियाँ ऑनलाइन पढ़ें एवं पीडीएफ में डाउनलोड करें।'
            : 'Discover, read, and download thousands of classic Hindi books, novels, religious texts, and literature in PDF format for free.';

        $canonicalUrl = url("/{$locale}");

        $hreflangs = self::buildHomeHreflangs();

        $structuredData = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $siteName,
                'url' => $canonicalUrl,
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => url("/{$locale}/search?q={search_term_string}"),
                    ],
                    'query-input' => 'required name=search_term_string',
                ],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => $siteName,
                'url' => url('/'),
                'logo' => asset('images/logo.png'),
            ],
        ];

        return new SeoData(
            title: $title,
            description: $description,
            canonicalUrl: $canonicalUrl,
            robots: 'index, follow',
            ogTitle: $title,
            ogDescription: $description,
            ogImage: asset('images/og-default.jpg'),
            ogType: 'website',
            ogUrl: $canonicalUrl,
            locale: $locale,
            twitterCard: 'summary_large_image',
            twitterTitle: $title,
            twitterDescription: $description,
            twitterImage: asset('images/og-default.jpg'),
            hreflangs: $hreflangs,
            breadcrumbs: [
                ['label' => 'Home', 'url' => null],
            ],
            structuredData: $structuredData,
        );
    }

    /**
     * Build SEO metadata and structured data for a Book detail page.
     */
    public static function forBook(Book $book, string $locale): SeoData
    {
        $siteName = config('app.name', 'Hindi PDF Library');

        // Resolve localized translation or fallback
        $translation = $book->localized($locale);
        $title = $translation?->title ?? $book->slug;

        // Resolve primary author name
        $primaryAuthor = $book->authors->first();
        $authorName = $primaryAuthor?->localized($locale)?->name ?? $primaryAuthor?->slug;

        // Resolve primary category
        $primaryCategory = $book->categories->first();
        $categoryName = $primaryCategory?->localized($locale)?->name ?? $primaryCategory?->slug;

        // Determine title priority: SEO title -> translated title + author -> fallback
        $seoTitle = $translation?->seo_title;
        if (empty($seoTitle)) {
            $seoTitle = $authorName
                ? "{$title} — {$authorName} | {$siteName}"
                : "{$title} | {$siteName}";
        }

        // Determine description priority: SEO description -> short_description -> description -> default
        $seoDescription = $translation?->seo_description;
        if (empty($seoDescription)) {
            if (! empty($translation?->short_description)) {
                $seoDescription = Str::limit(strip_tags($translation->short_description), 160);
            } elseif (! empty($translation?->description)) {
                $seoDescription = Str::limit(strip_tags($translation->description), 160);
            } else {
                $seoDescription = "Read and download '{$title}' in high-quality PDF format on {$siteName}. Available for online reading and download.";
            }
        }

        $canonicalUrl = url("/{$locale}/book/{$book->slug}");
        $robots = $book->isPublished() ? 'index, follow' : 'noindex, nofollow';

        // Cover media
        $coverMedia = $book->media->where('collection_name', 'cover')->first() ?? $book->media->first();
        $coverUrl = $coverMedia ? asset('storage/'.$coverMedia->file_name) : null;

        // Reciprocal hreflangs: only include languages that actually have a translation
        $hreflangs = [];
        $supported = config('localization.supported_locales', ['en' => [], 'hi' => []]);
        foreach (array_keys($supported) as $loc) {
            if ($book->hasTranslation($loc)) {
                $hreflangs[] = [
                    'locale' => $loc,
                    'url' => url("/{$loc}/book/{$book->slug}"),
                ];
            }
        }
        if ($book->hasTranslation('en')) {
            $hreflangs[] = [
                'locale' => 'x-default',
                'url' => url("/en/book/{$book->slug}"),
            ];
        }

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Home', 'url' => url("/{$locale}")],
        ];
        if ($primaryCategory) {
            $breadcrumbs[] = [
                'label' => $categoryName,
                'url' => url("/{$locale}/category/{$primaryCategory->slug}"),
            ];
        }
        $breadcrumbs[] = ['label' => $title, 'url' => null];

        // BreadcrumbList JSON-LD
        $breadcrumbElements = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => url("/{$locale}"),
            ],
        ];
        $pos = 2;
        if ($primaryCategory) {
            $breadcrumbElements[] = [
                '@type' => 'ListItem',
                'position' => $pos++,
                'name' => $categoryName,
                'item' => url("/{$locale}/category/{$primaryCategory->slug}"),
            ];
        }
        $breadcrumbElements[] = [
            '@type' => 'ListItem',
            'position' => $pos,
            'name' => $title,
            'item' => $canonicalUrl,
        ];

        // Book JSON-LD
        $bookSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Book',
            'name' => $title,
            'url' => $canonicalUrl,
            'description' => $seoDescription,
            'inLanguage' => $locale,
        ];

        if ($coverUrl) {
            $bookSchema['image'] = $coverUrl;
        }

        if ($authorName) {
            $bookSchema['author'] = [
                '@type' => 'Person',
                'name' => $authorName,
            ];
        }

        if ($book->publisher) {
            $bookSchema['publisher'] = [
                '@type' => 'Organization',
                'name' => $book->publisher,
            ];
        }

        if ($book->publication_year) {
            $bookSchema['datePublished'] = (string) $book->publication_year;
        }

        $isbn = $book->isbn13 ?? $book->isbn;
        if ($isbn) {
            $bookSchema['isbn'] = $isbn;
        }

        if ($book->pages) {
            $bookSchema['numberOfPages'] = (int) $book->pages;
        }

        // Truthful AggregateRating: strictly only if rating_count > 0 and rating_average > 0
        if ($book->rating_count > 0 && (float) $book->rating_average > 0) {
            $bookSchema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => number_format((float) $book->rating_average, 1),
                'bestRating' => '5',
                'worstRating' => '1',
                'ratingCount' => (int) $book->rating_count,
            ];
        }

        // Truthful Reviews: strictly approved reviews
        $approvedReviews = $book->relationLoaded('reviews')
            ? $book->reviews->where('status', ReviewStatus::Approved)->take(5)
            : $book->reviews()->approved()->with('user')->take(5)->get();

        if ($approvedReviews->isNotEmpty()) {
            $reviewsJson = [];
            foreach ($approvedReviews as $rev) {
                if (! empty($rev->body)) {
                    $reviewsJson[] = [
                        '@type' => 'Review',
                        'reviewRating' => [
                            '@type' => 'Rating',
                            'ratingValue' => (int) $rev->rating,
                            'bestRating' => 5,
                            'worstRating' => 1,
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name' => $rev->user?->name ?? 'Reader',
                        ],
                        'datePublished' => $rev->created_at?->toDateString() ?? date('Y-m-d'),
                        'reviewBody' => $rev->body,
                    ];
                }
            }
            if (! empty($reviewsJson)) {
                $bookSchema['review'] = $reviewsJson;
            }
        }

        $structuredData = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $breadcrumbElements,
            ],
            $bookSchema,
        ];

        return new SeoData(
            title: $seoTitle,
            description: $seoDescription,
            canonicalUrl: $canonicalUrl,
            robots: $robots,
            ogTitle: $seoTitle,
            ogDescription: $seoDescription,
            ogImage: $coverUrl ?? asset('images/og-default.jpg'),
            ogType: 'book',
            ogUrl: $canonicalUrl,
            locale: $locale,
            twitterCard: 'summary_large_image',
            twitterTitle: $seoTitle,
            twitterDescription: $seoDescription,
            twitterImage: $coverUrl ?? asset('images/og-default.jpg'),
            hreflangs: $hreflangs,
            breadcrumbs: $breadcrumbs,
            structuredData: $structuredData,
        );
    }

    /**
     * Build SEO metadata and structured data for an Author detail page.
     */
    public static function forAuthor(Author $author, string $locale): SeoData
    {
        $siteName = config('app.name', 'Hindi PDF Library');

        $translation = $author->localized($locale);
        $name = $translation?->name ?? $author->slug;

        $seoTitle = $translation?->seo_title;
        if (empty($seoTitle)) {
            $seoTitle = "{$name} — Biography, Books & PDF Downloads | {$siteName}";
        }

        $seoDescription = $translation?->seo_description;
        if (empty($seoDescription)) {
            if (! empty($translation?->biography)) {
                $seoDescription = Str::limit(strip_tags($translation->biography), 160);
            } else {
                $seoDescription = "Discover books, literary works, and biography of {$name} available to read and download on {$siteName}.";
            }
        }

        $canonicalUrl = url("/{$locale}/author/{$author->slug}");
        $robots = $author->is_active ? 'index, follow' : 'noindex, nofollow';

        // Reciprocal hreflangs
        $hreflangs = [];
        $supported = config('localization.supported_locales', ['en' => [], 'hi' => []]);
        foreach (array_keys($supported) as $loc) {
            if ($author->hasTranslation($loc)) {
                $hreflangs[] = [
                    'locale' => $loc,
                    'url' => url("/{$loc}/author/{$author->slug}"),
                ];
            }
        }
        if ($author->hasTranslation('en')) {
            $hreflangs[] = [
                'locale' => 'x-default',
                'url' => url("/en/author/{$author->slug}"),
            ];
        }

        $breadcrumbs = [
            ['label' => 'Home', 'url' => url("/{$locale}")],
            ['label' => $name, 'url' => null],
        ];

        $structuredData = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Home',
                        'item' => url("/{$locale}"),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $name,
                        'item' => $canonicalUrl,
                    ],
                ],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'Person',
                'name' => $name,
                'url' => $canonicalUrl,
                'description' => $seoDescription,
            ],
        ];

        return new SeoData(
            title: $seoTitle,
            description: $seoDescription,
            canonicalUrl: $canonicalUrl,
            robots: $robots,
            ogTitle: $seoTitle,
            ogDescription: $seoDescription,
            ogImage: asset('images/og-default.jpg'),
            ogType: 'profile',
            ogUrl: $canonicalUrl,
            locale: $locale,
            twitterCard: 'summary',
            twitterTitle: $seoTitle,
            twitterDescription: $seoDescription,
            hreflangs: $hreflangs,
            breadcrumbs: $breadcrumbs,
            structuredData: $structuredData,
        );
    }

    /**
     * Build SEO metadata and structured data for a Category page.
     */
    public static function forCategory(Category $category, string $locale): SeoData
    {
        $siteName = config('app.name', 'Hindi PDF Library');

        $translation = $category->localized($locale);
        $name = $translation?->name ?? $category->slug;

        $seoTitle = $translation?->seo_title;
        if (empty($seoTitle)) {
            $seoTitle = "{$name} Books (पुस्तकें) — Read & Download PDF | {$siteName}";
        }

        $seoDescription = $translation?->seo_description;
        if (empty($seoDescription)) {
            if (! empty($translation?->description)) {
                $seoDescription = Str::limit(strip_tags($translation->description), 160);
            } else {
                $seoDescription = "Explore our curated collection of {$name} books in Hindi and English. Download high quality PDFs on {$siteName}.";
            }
        }

        $canonicalUrl = url("/{$locale}/category/{$category->slug}");
        $robots = $category->is_active ? 'index, follow' : 'noindex, nofollow';

        // Reciprocal hreflangs
        $hreflangs = [];
        $supported = config('localization.supported_locales', ['en' => [], 'hi' => []]);
        foreach (array_keys($supported) as $loc) {
            if ($category->hasTranslation($loc)) {
                $hreflangs[] = [
                    'locale' => $loc,
                    'url' => url("/{$loc}/category/{$category->slug}"),
                ];
            }
        }
        if ($category->hasTranslation('en')) {
            $hreflangs[] = [
                'locale' => 'x-default',
                'url' => url("/en/category/{$category->slug}"),
            ];
        }

        $breadcrumbs = [
            ['label' => 'Home', 'url' => url("/{$locale}")],
        ];
        if ($category->parent) {
            $parentName = $category->parent->localized($locale)?->name ?? $category->parent->slug;
            $breadcrumbs[] = [
                'label' => $parentName,
                'url' => url("/{$locale}/category/{$category->parent->slug}"),
            ];
        }
        $breadcrumbs[] = ['label' => $name, 'url' => null];

        $breadcrumbElements = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => url("/{$locale}"),
            ],
        ];
        $pos = 2;
        if ($category->parent) {
            $parentName = $category->parent->localized($locale)?->name ?? $category->parent->slug;
            $breadcrumbElements[] = [
                '@type' => 'ListItem',
                'position' => $pos++,
                'name' => $parentName,
                'item' => url("/{$locale}/category/{$category->parent->slug}"),
            ];
        }
        $breadcrumbElements[] = [
            '@type' => 'ListItem',
            'position' => $pos,
            'name' => $name,
            'item' => $canonicalUrl,
        ];

        $structuredData = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $breadcrumbElements,
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $name,
                'url' => $canonicalUrl,
                'description' => $seoDescription,
            ],
        ];

        return new SeoData(
            title: $seoTitle,
            description: $seoDescription,
            canonicalUrl: $canonicalUrl,
            robots: $robots,
            ogTitle: $seoTitle,
            ogDescription: $seoDescription,
            ogImage: asset('images/og-default.jpg'),
            ogType: 'website',
            ogUrl: $canonicalUrl,
            locale: $locale,
            twitterCard: 'summary',
            twitterTitle: $seoTitle,
            twitterDescription: $seoDescription,
            hreflangs: $hreflangs,
            breadcrumbs: $breadcrumbs,
            structuredData: $structuredData,
        );
    }

    /**
     * Build SEO metadata and structured data for the Search & Discovery page.
     */
    public static function forSearch(string $locale, ?string $query = null): SeoData
    {
        $siteName = config('app.name', 'Hindi PDF Library');

        $isHindi = $locale === 'hi';
        $title = ! empty($query)
            ? ($isHindi ? "खोज परिणाम: {$query} | {$siteName}" : "Search Results: {$query} | {$siteName}")
            : ($isHindi ? "पुस्तकालय खोज | {$siteName}" : "Search Books & Literature | {$siteName}");

        $description = $isHindi
            ? 'हिन्दी पीडीएफ पुस्तकालय में शीर्षक, लेखक, श्रेणी या आईएसबीएन द्वारा पुस्तकें खोजें।'
            : "Search thousands of Hindi and English digital books by title, author, category, or ISBN on {$siteName}.";

        // Search canonical strips all query parameters
        $canonicalUrl = url("/{$locale}/search");

        // Per Phase 9 and 10 rules: Search queries and filter URLs should NOT be indexed
        $robots = 'noindex, follow';

        $hreflangs = self::buildHomeHreflangs('/search');

        $breadcrumbs = [
            ['label' => 'Home', 'url' => url("/{$locale}")],
            ['label' => $isHindi ? 'खोज' : 'Search', 'url' => null],
        ];

        $structuredData = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Home',
                        'item' => url("/{$locale}"),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $isHindi ? 'खोज' : 'Search',
                        'item' => $canonicalUrl,
                    ],
                ],
            ],
        ];

        return new SeoData(
            title: $title,
            description: $description,
            canonicalUrl: $canonicalUrl,
            robots: $robots,
            ogTitle: $title,
            ogDescription: $description,
            ogImage: asset('images/og-default.jpg'),
            ogType: 'website',
            ogUrl: $canonicalUrl,
            locale: $locale,
            twitterCard: 'summary',
            twitterTitle: $title,
            twitterDescription: $description,
            hreflangs: $hreflangs,
            breadcrumbs: $breadcrumbs,
            structuredData: $structuredData,
        );
    }

    /**
     * Helper to build hreflangs for standard static/catalogue pages.
     */
    private static function buildHomeHreflangs(string $path = ''): array
    {
        $hreflangs = [];
        $supported = config('localization.supported_locales', ['en' => [], 'hi' => []]);

        foreach (array_keys($supported) as $loc) {
            $hreflangs[] = [
                'locale' => $loc,
                'url' => url("/{$loc}{$path}"),
            ];
        }

        $hreflangs[] = [
            'locale' => 'x-default',
            'url' => url("/en{$path}"),
        ];

        return $hreflangs;
    }
}
