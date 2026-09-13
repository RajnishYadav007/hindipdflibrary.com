# Eloquent Relationships Reference

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Phase:** Phase 3 — Database & Core Data Model  
**Updated:** September 13, 2026  

---

## 1. Complete Relationship Topology

```
Book
 ├── belongsToMany Author (via book_authors, pivot: role, using: BookAuthor)
 ├── belongsToMany Category (via book_categories)
 ├── belongsToMany Genre (via book_genres)
 ├── belongsToMany Language (via book_languages)
 ├── belongsTo Language (originalLanguage via original_language_id)
 ├── hasMany BookTranslation (translations)
 ├── hasOne BookTranslation (translation(?locale))
 ├── hasMany Review (reviews)
 ├── hasMany Favorite (favorites)
 ├── morphMany Media (media)
 └── morphOne SeoMeta (seoMeta)

Author
 ├── belongsToMany Book (via book_authors, pivot: role, using: BookAuthor)
 ├── hasMany AuthorTranslation (translations)
 ├── hasOne AuthorTranslation (translation(?locale))
 ├── morphMany Media (media)
 └── morphOne SeoMeta (seoMeta)

Category
 ├── belongsTo Category (parent via parent_id)
 ├── hasMany Category (children via parent_id)
 ├── belongsToMany Book (via book_categories)
 ├── hasMany CategoryTranslation (translations)
 ├── hasOne CategoryTranslation (translation(?locale))
 └── morphOne SeoMeta (seoMeta)

Genre
 ├── belongsToMany Book (via book_genres)
 ├── hasMany GenreTranslation (translations)
 ├── hasOne GenreTranslation (translation(?locale))
 └── morphOne SeoMeta (seoMeta)

Language
 ├── belongsToMany Book (books via book_languages)
 └── hasMany Book (originalBooks via original_language_id)

User
 ├── hasMany Review (reviews)
 ├── hasMany Favorite (favorites)
 └── belongsToMany Book (favoriteBooks via favorites)

Review
 ├── belongsTo Book (book)
 └── belongsTo User (user)

Favorite
 ├── belongsTo Book (book)
 └── belongsTo User (user)

BookAuthor (Pivot)
 ├── belongsTo Book (book)
 └── belongsTo Author (author)

Media (Polymorphic)
 └── morphTo model (model)

SeoMeta (Polymorphic)
 └── morphTo seoable (seoable)

Redirect
 └── Standalone (Managed via path resolution)
```

---

## 2. Implementation Specifics by Model

### 2.1. `Book` Model
- **`authors()`**: `BelongsToMany` referencing `Author::class` through `book_authors` pivot using `BookAuthor::class` pivot model with `'role'` attribute and timestamps.
- **`categories()`**: `BelongsToMany` referencing `Category::class` through `book_categories`.
- **`genres()`**: `BelongsToMany` referencing `Genre::class` through `book_genres`.
- **`languages()`**: `BelongsToMany` referencing `Language::class` through `book_languages`.
- **`originalLanguage()`**: `BelongsTo` referencing `Language::class` via `original_language_id`.
- **`translations()`**: `HasMany` referencing `BookTranslation::class`.
- **`translation(?string $locale)`**: `HasOne` dynamically scoping to the given locale (defaults to `app()->getLocale()`).
- **`reviews()`**: `HasMany` referencing `Review::class`.
- **`favorites()`**: `HasMany` referencing `Favorite::class`.
- **`media()`**: `MorphMany` referencing `Media::class`.
- **`seoMeta()`**: `MorphOne` referencing `SeoMeta::class`.

### 2.2. `Author` Model
- **`books()`**: `BelongsToMany` referencing `Book::class` through `book_authors` using `BookAuthor::class` pivot.
- **`translations()`**: `HasMany` referencing `AuthorTranslation::class`.
- **`translation(?string $locale)`**: `HasOne` scoped to current/requested locale.
- **`media()`**: `MorphMany` referencing `Media::class` for portraits and autographs.
- **`seoMeta()`**: `MorphOne` referencing `SeoMeta::class`.

### 2.3. `Category` Model
- **`parent()`**: `BelongsTo` referencing self via `parent_id`.
- **`children()`**: `HasMany` referencing self via `parent_id`.
- **`books()`**: `BelongsToMany` referencing `Book::class` through `book_categories`.
- **`translations()`**: `HasMany` referencing `CategoryTranslation::class`.
- **`seoMeta()`**: `MorphOne` referencing `SeoMeta::class`.

### 2.4. `User` Model
- **`reviews()`**: `HasMany` referencing `Review::class`.
- **`favorites()`**: `HasMany` referencing `Favorite::class`.
- **`favoriteBooks()`**: `BelongsToMany` referencing `Book::class` through `favorites`.
