# Phase 6 — Query Performance & N+1 Prevention Strategy

## 1. Zero N+1 Assurance
Every public page controller query explicitly eager-loads relational dependencies.

### Example: Book Listing Queries
```php
$booksQuery = Book::published()
    ->with([
        'translations',
        'authors.translations',
        'categories.translations',
        'media',
    ]);
```
This loads the primary entities, translations, author relations, categories, and polymorphic media assets in a minimal, deterministic set of queries regardless of result set size.

## 2. Indexed Lookups
All public queries take advantage of indexes established in Phase 2 & 3 migrations:
- `books.slug`: Unique index
- `books.status`: Filter index
- `books.view_count`: Sort index
- `books.rating_average`: Sort index
- `books.isbn`, `books.isbn13`: Search indexes
- `authors.slug`: Unique index
- `categories.slug`: Unique index
- `translations.locale`: Foreign key + unique compound index (`book_id, locale`)

## 3. Atomic View Counting
Page views on the Book page use `incrementQuietly('view_count')`:
```php
$book->incrementQuietly('view_count');
```
This performs a direct atomic database increment without triggering updated_at timestamp updates or model events, minimizing write contention.
