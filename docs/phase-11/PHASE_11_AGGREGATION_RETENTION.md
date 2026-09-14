# Phase 11 — Aggregation & Data Retention Strategy

## 1. Aggregation Pipeline
To preserve shared-hosting performance, the Filament dashboard never scans millions of raw event rows during normal rendering:
1. **Raw Event Ingestion**:
   - Written immediately to `analytics_events` with indexed `occurred_at`.
2. **Hourly / Daily Rollups**:
   - `php artisan analytics:aggregate {--date=}` aggregates raw events for the target date into `analytics_daily` and `analytics_search_daily`.
   - **Idempotency**: Running the command multiple times for the same date cleanly replaces existing aggregated rows in a single atomic database transaction.
3. **Scheduler Integration (`routes/console.php`)**:
   - `analytics:aggregate` runs hourly.
   - `analytics:prune` runs daily.

## 2. Data Retention Policy
Configured via `config/analytics.php`:
- `retention.raw_events_days`: Default `30` days. Raw granular records are pruned after 30 days to reclaim storage on shared hosting.
- `retention.daily_aggregates_days`: Default `365` days. Summary reporting numbers are retained for long-term historical analysis.
- Cleanups are executed safely using chunked index deletes via `php artisan analytics:prune`.
