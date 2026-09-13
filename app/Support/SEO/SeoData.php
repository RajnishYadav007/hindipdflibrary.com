<?php

namespace App\Support\SEO;

readonly class SeoData
{
    /**
     * @param  array<int, array{locale: string, url: string}>  $hreflangs
     * @param  array<int, array{label: string, url: ?string}>  $breadcrumbs
     * @param  array<int, array<string, mixed>>  $structuredData
     */
    public function __construct(
        public string $title,
        public string $description,
        public string $canonicalUrl,
        public string $robots = 'index, follow',
        public ?string $ogTitle = null,
        public ?string $ogDescription = null,
        public ?string $ogImage = null,
        public string $ogType = 'website',
        public ?string $ogUrl = null,
        public string $locale = 'en',
        public string $twitterCard = 'summary_large_image',
        public ?string $twitterTitle = null,
        public ?string $twitterDescription = null,
        public ?string $twitterImage = null,
        public array $hreflangs = [],
        public array $breadcrumbs = [],
        public array $structuredData = [],
    ) {}
}
