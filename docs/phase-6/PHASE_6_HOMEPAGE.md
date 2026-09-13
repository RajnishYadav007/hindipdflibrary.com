# Phase 6 — Homepage Specification (`/`)

## 1. Structure & Layout
The homepage (`resources/views/welcome.blade.php`) is designed as a cultural discovery portal presenting 9 curated sections:

1. **Hero & Primary Discovery Search**:
   - Cultural trust badge: `डिजिटल हिन्दी पुस्तकालय धरोहर • Free & Legal Digital Library`.
   - Semantic `<form role="search">` sending queries directly to `route('search')`.
   - Popular search suggestion chips (e.g., गोदान, मधुशाला, प्रेमचंद, रश्मिरथी).
2. **Featured Literature (प्रमुख पुस्तकें)**:
   - Curated masterworks ordered by rating and review prominence.
3. **Popular & Trending (सर्वाधिक लोकप्रिय पुस्तकें)**:
   - High-engagement books ordered descending by `view_count`.
4. **Top Categories (श्रेणी अनुसार खोजें)**:
   - Primary literary taxonomies with dynamic count of published books.
5. **Distinguished Authors (प्रतिष्ठित लेखक)**:
   - Author profile cards with portrait avatars, published counts, and bio snippets.
6. **Highly Rated Works (उच्च मूल्यांकित रचनाएँ)**:
   - Filtered by `rating_average >= 4.0` with graceful fallback when threshold empty.
7. **Recently Added (नवीनतम जोड़ी गई पुस्तकें)**:
   - Newest library additions ordered by primary key ID / timestamp.
8. **Browse by Language (भाषा अनुसार संग्रह)**:
   - Multi-language exploration (Hindi, Sanskrit, etc.) linking to `/search?language={id}`.
9. **Cultural Trust & Public Domain Commitment**:
   - Transparent statement on legal open-access, public domain compliance, and preservation ethics.

## 2. Eager Loading & Query Performance
All queries executed in `HomeController` prevent N+1 overhead:
```php
$popularBooks = Book::published()
    ->with(['translations', 'authors.translations', 'categories.translations', 'media'])
    ->orderByDesc('view_count')
    ->take(8)
    ->get();
```
Fallback empty states ensure that even with zero database seeds, the layout renders cleanly and without exceptions.
