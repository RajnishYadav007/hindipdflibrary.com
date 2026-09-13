# Phase 6 — Book Detail Page Specification (`/books/{slug}`)

## 1. Overview
The book detail page (`resources/views/public/books/show.blade.php`) serves as the core reading discovery experience, combining complete bibliographic rigor with cultural context.

## 2. Page Sections & Component Breakdown
1. **Semantic Breadcrumb**:
   - `Home > [Category Name] > [Book Title]`.
2. **Hero Presentation**:
   - 2:3 aspect ratio book cover with high-resolution image rendering or fallback Devanagari typography card.
   - Title, edition, and author linkages.
   - Live rating stars and view count counter.
3. **Truthful Action CTAs**:
   - **Case 1 (Local Media Asset)**: Renders direct "Read / Download PDF" linking to verified storage file.
   - **Case 2 (External Archive)**: Renders "Access at Primary Source" with outbound `target="_blank" rel="noopener noreferrer nofollow"`.
   - **Case 3 (Bibliographic Archive Only)**: Displays "Reference Catalog Edition" badge. Zero misleading download buttons.
4. **Rich Content Presentation**:
   - Key Themes `#tags` parsed from JSON data.
   - Lead tagline (`short_description`) and full literary narrative (`description`).
   - Structural synopsis (`summary`).
   - Pedagogical rationale (`why_read`).
5. **Bibliographic Information Table**:
   - ISBN, ISBN-13, publisher, publication year, total pages, edition, language, and copyright/license status.
6. **Author Highlight Card**:
   - Biographical snapshot of the primary author with a link to their catalog.
7. **Community Reviews & Moderation**:
   - List of approved reviews via `<x-review-card />` with pagination.
   - Note on review moderation.
8. **Discovery Cross-links**:
   - More works by the same author (`$authorBooks`).
   - Related works within the same category (`$relatedBooks`).
9. **FAQ Accordion**:
   - Frequently asked questions regarding copyright, access, and document format.
