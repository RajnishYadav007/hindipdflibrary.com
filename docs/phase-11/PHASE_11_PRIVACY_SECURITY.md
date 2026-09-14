# Phase 11 — Privacy & Security Architecture

## 1. Data Minimization Principles
In strict compliance with privacy standards and modern web hygiene:
1. **No Raw IP Storage**: Raw IP addresses are **never** stored in the database.
2. **No Sensitive Telemetry**: Passwords, authentication cookies, OAuth tokens, payment details, and private PDF file contents are never captured.
3. **Pseudonymous Visitor Identification**:
   - `_hpl_vid`: Random 32-character alphanumeric token stored in an HttpOnly, Lax cookie with a 1-year lifespan.
   - `_hpl_sid`: 30-minute rolling session token.
   - These tokens cannot be reverse-engineered into personally identifiable information (PII).

## 2. Security & RBAC Enforcement
1. **Event Name Whitelisting**:
   - Arbitrary or forged event strings submitted via public endpoints are discarded. Only events defined in `App\Support\Analytics\AnalyticsEvent::all()` are accepted.
2. **Filament Admin Authorization**:
   - The `/admin/analytics` page and all its individual widgets implement strict access gates checking `$user->hasRole('Super Admin') || $user->can('analytics.view')`.
   - Unauthorized attempts by visitors, subscribers, or non-admin roles receive an immediate 403 Forbidden response.
3. **SQL Injection Defense**:
   - All search queries, referrers, and user agents are sanitized, trimmed, and persisted via PDO parameterized queries.
