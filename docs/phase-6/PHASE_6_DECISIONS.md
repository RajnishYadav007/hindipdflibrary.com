# Phase 6 — Architectural & Technical Decisions Log

## 1. Decision: Singular URL Aliases with 301 Redirects
- **Context**: In Phase 0-5 architecture discussions, standard plural routes (`/books/{slug}`, `/authors/{slug}`, `/categories/{slug}`) were chosen for REST consistency. However, external links and user expectations commonly refer to singular URLs (`/book/{slug}`, `/author/{slug}`, `/category/{slug}`).
- **Decision**: Implemented singular URL routes that issue HTTP 301 Permanent Redirects to the canonical plural endpoints.
- **Consequence**: Avoids 404 dead links, consolidates SEO ranking signals, and retains standard Laravel resource convention.

## 2. Decision: Truthful CTAs for Digital Media
- **Context**: Many digital library websites display fake or dead "Download PDF" buttons even when a book is purely an archival record or reference catalog entry.
- **Decision**: Strict CTA logic was implemented in `BookController` and `show.blade.php`:
  1. If a verified local PDF media asset exists: render direct Read/Download button.
  2. If an external `source_url` exists: render "Access at Primary Source".
  3. If neither exists: display "Reference Catalog Edition" badge with clear contextual explanation.
- **Consequence**: High trust, transparent user experience with zero dead-end user frustration.

## 3. Decision: Search Query Strategy for Phase 6
- **Context**: `AGENTS.md` and Phase 1-2 rules specify that Meilisearch or external search daemons are not permitted in shared hosting V1.
- **Decision**: Implemented relational SQL query filtering across book translations, author translations, and ISBN columns using Eloquent eager-loaded relationship matching (`whereHas`).
- **Consequence**: Zero daemon requirements, 100% compatibility with shared hosting MySQL 8, while leaving room for Fulltext or Meilisearch integration in future phases.

## 4. Decision: Strict Phase 7 Boundary Enforcement
- **Context**: User interactions like rating submission, writing reviews, and saving bookmarks to user database are Phase 7 features.
- **Decision**: Phase 6 strictly limits itself to core public read views. Review cards display only approved reviews; review submission forms and persistent favorites are strictly reserved for Phase 7.
- **Consequence**: Prevented scope creep and kept code lightweight and focused.
