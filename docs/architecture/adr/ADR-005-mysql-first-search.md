# ADR-005: MySQL-First Fulltext Search Strategy

**Date:** September 13, 2026  
**Status:** Accepted  
**Deciders:** Lead Solutions Architect, Senior Database Architect  

---

## Context
A digital library lives or dies by its search experience. Users search by book title, author, description, category, and ISBN in both English and Hindi. However, our V1 infrastructure is shared hosting, where standalone background search server daemons (Meilisearch, Elasticsearch) cannot be installed or executed.

## Decision
Implement **MySQL 8 FULLTEXT Indexing with Natural Language & Boolean Matching** for V1 search, with a structured migration path to Meilisearch in Phase 2+.

## Reason
1. **Zero External Dependencies:** Runs entirely inside the existing MySQL relational database. Requires no memory allocation for separate Java or Rust processes.
2. **High Performance at V1 Scale:** For catalogs under 50,000 titles, MySQL InnoDB FULLTEXT indexes execute multi-column boolean searches in sub-15ms.
3. **Multilingual UTF-8 Support:** Supports Hindi Devanagari script natively when tables use `utf8mb4` encoding with `utf8mb4_unicode_ci` collation.
4. **Phonetic Transliteration Matching:** Adding a `title_transliterated` column allows matching Latin-spelled Hindi searches (e.g., *"Premchand"*) against Devanagari records (*"प्रेमचंद"*).

## Alternatives Considered
- **Meilisearch in V1:** Impossible on shared hosting (cannot run standalone binary or persistent background daemon).
- **Basic SQL `LIKE '%term%'` Queries:** Inefficient; causes full table scans on large catalogs, destroys database performance, and cannot rank by relevance.

## Consequences
- Requires composite `FULLTEXT` index on `(title, title_transliterated, description)` on `books` table.
- Advanced typo-tolerance (fuzzy matching) is limited compared to dedicated search engines.

## Future Migration Path
When migrating to VPS in Phase 2+, install `laravel/scout` and Meilisearch, configure `SCOUT_DRIVER=meilisearch`, and run `scout:import` without touching public search controllers.
