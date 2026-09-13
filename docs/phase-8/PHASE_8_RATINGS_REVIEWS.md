# Phase 8 — Ratings & Reviews Specification

## 1. Core Rating System
- **Scale**: Integer ratings from 1 to 5.
- **Constraints**: Minimum 1, Maximum 5. Non-integers or values outside this range are rejected by server validation.
- **Storage**: Stored in the `rating` column (`TINYINT UNSIGNED`) of the `reviews` table.
- **Uniqueness Invariant**: Exactly one rating/review per user per book, enforced at the database level via `UNIQUE(user_id, book_id)`.

## 2. Review Content
- **Title**: Optional string (max 150 characters), trimmed and sanitized.
- **Body**: Optional text (max 2000 characters), trimmed and sanitized.
- **Verification**: `is_verified` flag defaults to `true` for authenticated registered users.
- **Anonymized Auditing**: `ip_hash` (SHA-256) and `user_agent_hash` (SHA-256) recorded for spam analysis without storing raw PII.

## 3. Submission & Update Logic (`SubmitReviewAction`)
- When an authenticated user submits a rating for a book:
  - If no prior review exists: creates a new review with `status = 'pending'`.
  - If a prior review exists: updates `rating`, `title`, and `body`, resetting `status = 'pending'` to prevent unmoderated text tampering.
- Only published books (`BookStatus::Published`) accept reviews. Attempting to submit a review for a draft or archived book throws an `InvalidArgumentException`.
