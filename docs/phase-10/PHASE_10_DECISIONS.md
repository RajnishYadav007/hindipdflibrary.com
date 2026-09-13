# Phase 10 — Key Architectural Decisions

## 1. Zero External Search Daemons in V1
- **Context**: Meilisearch, Elasticsearch, and Typesense require dedicated persistent background daemons and significant RAM.
- **Decision**: MySQL/MariaDB parameterized SQL scoring with composite indexes in V1, wrapped in `SearchEngineInterface`.
- **Rationale**: Shared hosting (StackCP/ServerByt) lacks daemon supervisor access. Abstracting behind an interface enables 100% compatibility today and a zero-downtime migration to Meilisearch in V2.

## 2. Parameterized SQL Relevance Scoring vs Native FULLTEXT Alone
- **Context**: Shared hosting MySQL/MariaDB environments often set `ft_min_word_len=4` and lack custom stopword/tokenizers for Indic/Hindi Unicode text.
- **Decision**: Implemented deterministic SQL scoring evaluating exact title matches, prefixes, substrings, ISBN matches, author names, and category names.
- **Rationale**: Guarantees consistent Unicode Hindi and Sanskrit token matching regardless of underlying MySQL server configuration or hosting provider limitations.

## 3. Empty Search Result Recommendations
- **Context**: Queries with 0 matches often lead to immediate user bounce.
- **Decision**: The search view checks `$books->isEmpty()` and invokes `BookDiscoveryService::popularBooks()` to present curated alternative titles.
- **Rationale**: Boosts user engagement and discovery even when specific keywords yield no direct hits.

## 4. Search Page Robots and Canonical Tags
- **Context**: Search query pages can produce infinite URL parameter combinations, causing crawl budget depletion and duplicate content indexing.
- **Decision**: Forced `noindex, follow` on all search routes, with canonical URLs pointing strictly to `route('search')` without query strings.
- **Rationale**: Preserves domain search authority while directing Googlebot crawl budget toward authoritative book, author, and category pages.
