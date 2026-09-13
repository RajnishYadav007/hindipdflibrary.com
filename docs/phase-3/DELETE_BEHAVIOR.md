# Foreign Key & Delete Behavior Strategy

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Phase:** Phase 3 — Database & Core Data Model  
**Updated:** September 13, 2026  

---

## 1. Deletion Philosophy

In a production digital library platform, unconsidered cascading deletes can lead to catastrophic data loss (e.g., deleting a category accidentally deleting hundreds of books, or deleting an author purging books from other co-authors). 

Every foreign key relationship in the database is configured with deliberate, defensive delete behaviors.

---

## 2. Foreign Key Delete Action Matrix

| Foreign Key | Target Table | On Delete Action | Architectural Rationale |
| :--- | :--- | :--- | :--- |
| `categories.parent_id` | `categories.id` | **`SET NULL`** (`nullOnDelete()`) | Deleting a parent category must NEVER delete its child subcategories; children become top-level categories. |
| `books.original_language_id` | `languages.id` | **`SET NULL`** (`nullOnDelete()`) | Deleting a language record must not destroy books; historical original language link is simply cleared. |
| `book_translations.book_id` | `books.id` | **`CASCADE`** (`cascadeOnDelete()`) | Translations cannot exist without their parent book. |
| `author_translations.author_id` | `authors.id` | **`CASCADE`** (`cascadeOnDelete()`) | Localized bios cannot exist without the author entity. |
| `category_translations.category_id`| `categories.id` | **`CASCADE`** (`cascadeOnDelete()`) | Localized category names cannot exist without the category. |
| `genre_translations.genre_id` | `genres.id` | **`CASCADE`** (`cascadeOnDelete()`) | Localized genre names cannot exist without the genre. |
| `book_authors.book_id` | `books.id` | **`CASCADE`** (`cascadeOnDelete()`) | Pivot binding removed when book is permanently purged. |
| `book_authors.author_id` | `authors.id` | **`CASCADE`** (`cascadeOnDelete()`) | Pivot binding removed when author is purged; book remains intact. |
| `book_categories.book_id` | `books.id` | **`CASCADE`** (`cascadeOnDelete()`) | Pivot binding removed when book is purged. |
| `book_categories.category_id` | `categories.id`| **`CASCADE`** (`cascadeOnDelete()`) | Deleting a category purges only the pivot link, **NEVER** deleting associated books. |
| `book_genres.book_id` | `books.id` | **`CASCADE`** (`cascadeOnDelete()`) | Pivot binding removed when book is purged. |
| `book_genres.genre_id` | `genres.id` | **`CASCADE`** (`cascadeOnDelete()`) | Deleting a genre purges only the pivot link, **NEVER** deleting books. |
| `book_languages.book_id` | `books.id` | **`CASCADE`** (`cascadeOnDelete()`) | Pivot binding removed when book is purged. |
| `book_languages.language_id` | `languages.id` | **`CASCADE`** (`cascadeOnDelete()`) | Deleting a language purges only the availability link, preserving the book. |
| `reviews.book_id` | `books.id` | **`CASCADE`** (`cascadeOnDelete()`) | Reviews purged if parent book is permanently deleted. |
| `reviews.user_id` | `users.id` | **`CASCADE`** (`cascadeOnDelete()`) | Reviews purged if user permanently deletes their account. |
| `favorites.book_id` | `books.id` | **`CASCADE`** (`cascadeOnDelete()`) | Favorites purged if book is permanently deleted. |
| `favorites.user_id` | `users.id` | **`CASCADE`** (`cascadeOnDelete()`) | Favorites purged if user permanently deletes their account. |

---

## 3. Soft Delete Implementation

Soft deletes (`SoftDeletes` trait and `deleted_at` timestamp) are deliberately applied to key business entities:
1. **`books`**: A deleted book retains its database identity so incoming requests can be gracefully handled via 301/410 redirects rather than throwing abrupt database errors.
2. **`authors`**: An author removed from public display remains archived to preserve historical book attribution.
3. **`categories`**: Prevents accidental permanent removal during taxonomy reorganizations.
4. **`genres`**: Preserves historical classification tagging.

Tables that **do NOT** use soft deletes:
- `reviews`, `favorites`: User-generated interactions are cleanly dropped on purge.
- `book_translations`, `author_translations`: Tightly bound to their parent models' lifecycles.
- `book_authors`, `book_categories`, `book_genres`, `book_languages`: Pure relational pivots.
