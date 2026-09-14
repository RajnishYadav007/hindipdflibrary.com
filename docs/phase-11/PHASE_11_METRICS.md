# Phase 11 — Metric Definitions & Calculations

## 1. Visitor & Session Measurement
- **Visitor (`visitor_id`)**: A pseudonymous 32-character random string stored in an HttpOnly, SameSite=Lax cookie (`_hpl_vid`) valid for 1 year. Zero personal information, IP addresses, or browser fingerprinting are used.
- **Session (`session_id`)**: A rolling 30-minute inactivity window stored in cookie `_hpl_sid`. Any request made within 30 minutes refreshes the cookie.
- **Page Views**: Total raw count of successful GET requests to tracked public pages.
- **New Users**: Count of registered users created on the specified date (`User::whereBetween('created_at', ...)`).

## 2. Book Performance Rankings
- **Most Viewed**: Ranked by `view_count DESC`.
- **Most Read**: Ranked by total count of `book_read` events.
- **Most Downloaded**: Ranked by `downloads_count DESC`.
- **Most Favorited**: Ranked by `favorites_count DESC`.
- **Most Rated**: Ranked by cached `rating_count DESC`.
- **Highest Rated**: Ranked by cached `rating_average DESC` with an unyielding **minimum rating threshold of $\ge 3$ ratings** to prevent single 5-star ratings from dominating editorial leaderboards.
- **Trending**: Measured via 7-day velocity ($\Delta$ views over the trailing 7 days).

## 3. Search Intelligence & CTR
- **Search Volume**: Total count of search queries submitted.
- **Zero-Result Searches**: Searches where `search_result_count = 0` or `null`. Serves as high-priority catalog acquisition signals.
- **Search-to-Book CTR**: $\frac{\text{click\_count}}{\text{search\_count}} \times 100\%$.
