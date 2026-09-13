# Phase 6 — Category Page Specification (`/categories/{slug}`)

## 1. Overview
The category catalog page (`resources/views/public/categories/show.blade.php`) handles the thematic exploration of the library's holdings.

## 2. Layout & Features
1. **Hierarchical Breadcrumb Navigation**:
   - Dynamically resolves parent categories (e.g., `Home > Literature > Novels`).
2. **Category Header**:
   - Category name and full editorial description.
   - Total published books count in category.
3. **Subcategory Taxonomy Badges**:
   - Lists child categories with count of published books.
4. **Book Collection Grid**:
   - Grid layout of all published books in this category.
   - Clean pagination links (`{{ $books->links() }}`).
5. **Security & Visibility Scoping**:
   - Returns HTTP 404 if category is inactive (`is_active = false`).
   - Draft and archived books are completely excluded from category counts and listing.
