# Phase 9 — Key Architectural Decisions

## 1. No Third-Party SEO Packages (Spatie SEO / Artesaos)
- **Context**: Several popular Laravel SEO packages exist.
- **Decision**: Implemented native `SeoData` DTO and `SeoManager` service.
- **Rationale**: Third-party packages introduce overhead, configuration bloat, and rigid schema assumptions. A native lightweight DTO gives 100% control over multilingual alternates, canonical URL sanitization, and structured JSON-LD without dependency drift.

## 2. Shared-Hosting Safe XML Sitemap
- **Context**: Digital libraries with thousands of books can exhaust PHP memory limits when generating dynamic XML sitemaps.
- **Decision**: Implemented chunked query streaming (`chunkById(250)`) with direct `response()->stream()` output.
- **Rationale**: Minimal memory footprint (under 20MB) even with tens of thousands of items, compatible with shared hosting without requiring background file writes.

## 3. Truthful AggregateRating Output
- **Context**: Some sites emit dummy or default 5.0 ratings when books have 0 reviews.
- **Decision**: Omit `aggregateRating` and `review` entirely in JSON-LD if `rating_count === 0` or if reviews are unapproved.
- **Rationale**: Prevents Google Search Console penalties and Rich Snippet disqualification for deceptive structured data.

## 4. Backwards-Compatible Legacy Routing
- **Context**: Existing URLs may lack the `/{locale}` prefix.
- **Decision**: Localized route groups use prefix `/{locale}`, while root fallback routes serve the default locale (`en`) with canonical links pointing to the authoritative canonical URL.
- **Rationale**: Ensures zero 404 errors for existing bookmarks, backlinks, and search index entries.
