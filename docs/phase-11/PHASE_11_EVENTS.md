# Phase 11 — Analytics Event Catalog & Capture Rules

## 1. Allowed Event Types
All event names are strictly validated against `App\Support\Analytics\AnalyticsEvent`:

| Event Name | Trigger Location | Captured Attributes |
| :--- | :--- | :--- |
| `page_view` | `CaptureAnalyticsEvent` middleware | `url_path`, `source`, `device_type`, `visitor_id`, `session_id` |
| `book_view` | `BookController@show` | `book_id`, `source`, `device_type`, `visitor_id` |
| `book_read` | `BookController@read` | `book_id`, `user_id` (if auth), `device_type` |
| `book_download` | `BookController@download` | `book_id`, `user_id` (if auth), `device_type` |
| `book_favorite` | `FavoriteController@toggle` | `book_id`, `user_id` |
| `book_unfavorite` | `FavoriteController@toggle` | `book_id`, `user_id` |
| `rating_submitted` | `SubmitReviewAction@handle` | `book_id`, `user_id`, `metadata.rating` |
| `review_submitted` | `SubmitReviewAction@handle` | `book_id`, `user_id` |
| `search` | `SearchController@index` | `search_query`, `search_result_count`, `locale` |
| `search_result_click` | `SearchController@click` | `book_id`, `search_query` |
| `author_view` | `AuthorController@show` | `author_id`, `visitor_id`, `source` |
| `category_view` | `CategoryController@show` | `category_id`, `visitor_id`, `source` |
| `language_switch` | Language Switcher Component | `locale`, `visitor_id` |

## 2. Validation & Invariant Rules
- Any event not explicitly present in `AnalyticsEvent::all()` is discarded and logged with a warning.
- `book_id` is validated against the database; non-existent books are set to `null` to avoid relational constraint violations.
- Draft books return a 404 response on public pages and **never** trigger `book_view` events.
- Search queries are trimmed and capped at 255 characters.
