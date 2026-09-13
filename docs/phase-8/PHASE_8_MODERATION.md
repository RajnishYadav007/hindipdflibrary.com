# Phase 8 — Review Moderation & Workflow Specification

## 1. Lifecycle State Machine
```
User Submission ──► [pending]
                        │
       ┌────────────────┼────────────────┐
       ▼                ▼                ▼
  [approved]       [rejected]         [spam]
       │                │                │
(Publicly Visible  (Hidden from     (Hidden from
 & Counted in       Public & Not     Public & Not
 Book Metrics)      Counted)         Counted)
```

## 2. Moderation States (`ReviewStatus` Enum)
- **`pending`**: Newly submitted or revised reviews awaiting admin approval. Not visible on public pages; does not contribute to `rating_average` or `rating_count`.
- **`approved`**: Verified by a moderator. Rendered publicly on the book page; contributes directly to `rating_average`, `rating_count`, and `review_count`.
- **`rejected`**: Moderated as unsuitable or substandard. Excluded from public book pages and metrics.
- **`spam`**: Flagged as malicious or promotional spam. Excluded from public book pages and metrics.

## 3. Filament Admin Interface
- Located at `/admin/reviews` (`ReviewResource`).
- Row actions available:
  - **Approve**: Sets status to `approved`.
  - **Reject**: Sets status to `rejected`.
  - **Mark Spam**: Sets status to `spam`.
- Bulk actions available: `Approve Selected`, `Reject Selected`, `Mark as Spam`.
- Fields `book_id`, `user_id`, and `rating` are disabled in the edit form to prevent reassignment of review authorship or book linkage.
