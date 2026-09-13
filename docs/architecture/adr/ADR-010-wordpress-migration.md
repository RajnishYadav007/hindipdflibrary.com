# ADR-010: Zero-Downtime WordPress Migration & SEO Traffic Preservation

**Date:** September 13, 2026  
**Status:** Accepted  
**Deciders:** Lead Solutions Architect, SEO Architect, Database Architect  

---

## Context
The current live website `hindipdflibrary.com` is an active WordPress site with existing Google rankings, backlinks, and user traffic. A botched migration where old URLs return HTTP 404 would cause an immediate catastrophic loss of organic search traffic, domain authority, and user trust.

## Decision
Execute a **three-stage non-destructive migration and permanent 301 redirection pipeline**:
1. **Extraction (Read-Only):** Export WordPress posts, metadata, categories, tags, and media URLs to structured JSON/SQL dumps without modifying the live database.
2. **Transformation & Mapping:** An ETL migration command in Laravel normalizes WordPress data into the new relational schema, populating a dedicated `legacy_redirects` lookup table:
   - `old_path`: e.g., `/munshi-premchand-godan-pdf-download/`
   - `new_slug`: e.g., `/en/books/godan`
   - `status_code`: `301`
3. **Redirection Middleware:** High-performance cached middleware intercepts legacy requests and issues permanent 301 redirects, preserving 100% of link equity (PageRank) to the new Laravel platform.

## Reason
1. **Preserve SEO Capital:** Guarantees every historical URL indexed by Google seamlessly lands on the corresponding book page.
2. **Production Safety:** The live WordPress site remains 100% untouched until final staging sign-off.
3. **Auditability:** Every transformed record retains its original WordPress post ID (`wp_post_id`) for retrospective data integrity verification.

## Alternatives Considered
- **Direct Apache `.htaccess` Rewrite Rules:** Managing 5,000+ regex redirect rules inside `.htaccess` slows down every single Apache request and is prone to syntax bugs.
- **Ignoring Legacy Permalinks:** Unacceptable. Causes immediate 70-90% organic search traffic collapse.

## Consequences
- Requires maintaining a `legacy_redirects` table indexed on `old_path`.
- Legacy URL lookups are cached in memory/file cache for sub-millisecond execution.

## Future Migration Path
After 12-18 months, once Google Search Console demonstrates that 99%+ of crawl requests use the new URLs, legacy redirect entries can be pruned or archived.
