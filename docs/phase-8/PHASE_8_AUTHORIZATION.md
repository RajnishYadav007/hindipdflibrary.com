# Phase 8 — Authorization & Security Specification

## 1. Authentication Scoping
- Only authenticated users (`auth()->check()`) can submit or update a rating or review.
- Guests are prevented from submitting at both the presentation level (clear sign-in callout rendered by `<livewire:book-review-form />`) and the backend action level (redirection to login).

## 2. Ownership & Anti-Tampering Rules
- A user can only submit, view, and update their own review record (`user_id = auth()->id()`).
- Form submission does NOT accept a client-provided `user_id` or `status` parameter, completely mitigating parameter tampering or mass-assignment attacks.
- Review moderation status can only be modified by authorized staff holding `Super Admin`, `Admin`, or `Moderator` roles via Filament authorization policies.

## 3. Rate Limiting & Privacy
- User IP addresses and User-Agent headers are hashed using SHA-256 before storage (`ip_hash`, `user_agent_hash`), preventing storage of raw PII while still enabling spam and anomaly detection.
