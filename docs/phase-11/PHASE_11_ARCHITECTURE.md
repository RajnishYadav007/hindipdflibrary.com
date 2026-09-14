# Phase 11 — Analytics & Admin Intelligence Architecture

## 1. System Topology & Layers

```
                                  Public HTTP Request
                                           │
                                           ▼
                       App\Http\Middleware\CaptureAnalyticsEvent
                                           │
                       (Extracts _hpl_vid, _hpl_sid, device, source)
                                           │
                     ┌─────────────────────┴─────────────────────┐
                     ▼                                           ▼
             Sends HTTP Response                       Runs via defer(...)
           (Zero visitor latency)                                │
                                                                 ▼
                                                  App\Services\Analytics\AnalyticsService
                                                                 │
                                                       (Whitelist validation)
                                                                 │
                                                                 ▼
                                                    Database: analytics_events
                                                                 │
                                                                 ▼
                                           Scheduled Job: analytics:aggregate (Hourly)
                                                                 │
                                           ┌─────────────────────┴─────────────────────┐
                                           ▼                                           ▼
                                    analytics_daily                         analytics_search_daily
                                           │                                           │
                                           └─────────────────────┬─────────────────────┘
                                                                 ▼
                                             Filament 5 Admin Intelligence Dashboard
                                                   (App\Filament\Pages\AnalyticsDashboard)
                                                   - TodayStatsWidget
                                                   - TrafficChartWidget
                                                   - TopBooksWidget
                                                   - SourcesWidget
                                                   - SearchWidget
```

## 2. Table Specifications & Indexes

### `analytics_events` (Raw Event Log)
- `id`: unsignedBigInteger auto_increment
- `event_name`: varchar(64) index
- `user_id`: unsignedBigInteger nullable index
- `visitor_id`: varchar(64) nullable index (pseudonymous 32-char UUID)
- `session_id`: varchar(64) nullable index (30-minute rolling window)
- `book_id`: unsignedBigInteger nullable index
- `author_id`: unsignedBigInteger nullable index
- `category_id`: unsignedBigInteger nullable index
- `search_query`: varchar(255) nullable
- `search_result_count`: unsignedInteger nullable
- `source`: varchar(32) nullable index (`google`, `direct`, `social`, `referral`, `other`)
- `country`: varchar(8) default `'unknown'` index
- `device_type`: varchar(16) default `'unknown'` index (`mobile`, `desktop`, `tablet`, `unknown`)
- `browser`: varchar(32) nullable
- `operating_system`: varchar(32) nullable
- `url_path`: varchar(512) nullable
- `metadata`: json nullable
- `occurred_at`: timestamp index
- Composite Indexes:
  - `idx_events_name_time`: `[event_name, occurred_at]`
  - `idx_events_book_name_time`: `[book_id, event_name, occurred_at]`
  - `idx_events_time_source`: `[occurred_at, source]`

### `analytics_daily` (Summary Aggregates)
- `id`: unsignedBigInteger auto_increment
- `date`: date index
- `event_name`: varchar(64) index
- `book_id`: unsignedBigInteger nullable index
- `source`: varchar(32) default `'other'`
- `country`: varchar(8) default `'unknown'`
- `device_type`: varchar(16) default `'unknown'`
- `event_count`: unsignedInteger default 0
- `unique_visitors`: unsignedInteger default 0
- `unique_sessions`: unsignedInteger default 0
- Composite Indexes:
  - `idx_daily_date_event`: `[date, event_name]`
  - `idx_daily_date_book`: `[date, book_id]`
  - `idx_daily_event_book`: `[event_name, book_id]`

### `analytics_search_daily` (Search Intelligence)
- `id`: unsignedBigInteger auto_increment
- `date`: date index
- `search_query`: varchar(255) index
- `locale`: varchar(8) default `'en'`
- `search_count`: unsignedInteger default 0
- `zero_result_count`: unsignedInteger default 0
- `click_count`: unsignedInteger default 0
- Composite Index:
  - `idx_search_daily_date_query`: `[date, search_query]`
