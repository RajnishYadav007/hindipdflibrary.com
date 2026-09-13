# Phase 9 — SEO + Multilingual Architecture

## 1. System Topology & Layers

```
                                    HTTP Request
                                         │
                                         ▼
                            App\Http\Middleware\SetLocale
                                         │
                 ┌───────────────────────┴───────────────────────┐
                 ▼                                               ▼
     Localized Route (/{locale}/...)               Root Route (Legacy Fallback)
                 │                                               │
                 ▼                                               ▼
     Locale set in App & URL                         Default locale 'en' used
                 │                                               │
                 └───────────────────────┬───────────────────────┘
                                         ▼
                               Presentation Layer
                 ┌───────────────────────────────────────────────┐
                 │ Public Blade Views & Base Layout              │
                 │   - components/seo-head.blade.php             │
                 │   - components/language-switcher.blade.php    │
                 │   - Controllers: Book, Author, Category, etc. │
                 └───────────────────────┬───────────────────────┘
                                         │
                                         ▼
                                 Application Layer
                 ┌───────────────────────────────────────────────┐
                 │ App\Services\SEO\SeoManager                   │
                 │ App\Support\SEO\SeoData (Immutable DTO)       │
                 │ App\Http\Controllers\SitemapController        │
                 │ App\Http\Controllers\RobotsController         │
                 └───────────────────────┬───────────────────────┘
                                         │
                                         ▼
                             Domain / Persistence Layer
                 ┌───────────────────────────────────────────────┐
                 │ App\Models\Concerns\HasTranslations           │
                 │ App\Models\Book, Author, Category, Genre      │
                 │ App\Models\BookTranslation, etc.              │
                 └───────────────────────────────────────────────┘
```

## 2. Localization Configuration (`config/localization.php`)
- Supported Locales:
  - `en` (English - default / canonical fallback / x-default)
  - `hi` (Hindi - primary platform focus)
  - `sa` (Sanskrit - classical/cultural domain)
- Auto-configured URL default parameters ensure route generation helper `route('books.show', $book)` seamlessly includes the active locale context without manual parameter arrays.

## 3. SEO Data Transfer Object (`App\Support\SEO\SeoData`)
An immutable DTO capturing complete metadata:
- `title` and `description`
- `canonicalUrl` (normalized without tracking parameters or query strings)
- `alternateUrls` (map of locale code to full localized URL + `x-default`)
- `ogType`, `ogImage`
- `robots` (e.g., `index, follow` or `noindex, follow`)
- `schema` (array structure serialized directly into `<script type="application/ld+json">`)

## 4. Truthful Schema.org Policy
In strict compliance with Google's Search Central Structured Data Guidelines:
- `AggregateRating` is rendered ONLY when `rating_count > 0` with approved ratings.
- `Review` items are limited to the top 5 approved, non-spam reviews.
- `BreadcrumbList` reflects the exact hierarchy (`Home > Books > {Title}`).
