# ADR-008: Privacy-First First-Party Analytics Without Performance Degradation

**Date:** September 13, 2026  
**Status:** Accepted  
**Deciders:** Lead Solutions Architect, Database Architect, Performance Engineer  

---

## Context
Publishing and digital library platforms require detailed intelligence: which books are trending, search term volume, reading time, download counts, and referral sources. Relying solely on Google Analytics creates ad-blocker blind spots (losing 30-40% of tech-savvy traffic) and privacy compliance concerns. Conversely, running synchronous database insert queries on every page hit severely degrades server performance on shared MySQL.

## Decision
Implement a **buffered first-party analytics engine**:
1. Pageviews and book events write to a lightweight, non-indexed ingestion buffer table (`page_views_buffer`).
2. An hourly scheduled task aggregates raw events into daily summary reporting tables (`book_analytics_daily`, `search_analytics_daily`, `traffic_analytics_daily`) and truncates the buffer.
3. Filament dashboard displays aggregated reports with zero real-time query overhead on user requests.

## Reason
1. **Zero User-Perceived Latency:** Inserting a lean 5-column row into an unindexed buffer table takes $< 2\text{ms}$.
2. **100% Data Ownership:** Bypasses browser ad-blockers and tracking protection.
3. **No Database Bloat:** By aggregating and pruning hourly, the database avoids unbounded storage growth on shared hosting.

## Alternatives Considered
- **Direct Synchronous Model Counter Updates (`Book::increment('views')`):** Causes table row locks on popular books, crippling database concurrency.
- **Third-Party SaaS (Mixpanel / Plausible Cloud):** High recurring subscription cost; sends sensitive reader telemetry offsite.

## Consequences
- Requires background aggregation command running on scheduler.
- Analytics numbers update on an hourly cadence rather than instantaneous real-time (acceptable for library reporting).

## Future Migration Path
If traffic exceeds 500,000 daily pageviews, migrate the buffer table to a dedicated Redis stream or ClickHouse columnar database instance on cloud infrastructure.
