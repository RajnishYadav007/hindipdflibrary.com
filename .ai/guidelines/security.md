# AI Guidelines: Security & Defensive Coding

## 1. Secrets Management
- NEVER hardcode API keys, passwords, or connection strings in PHP code or Blade views.
- Always use `config('services.google.client_id')` mapped from `env()`.

## 2. Authentication & Authorization
- Use Laravel's built-in authentication guards.
- Implement Laravel Policies for all user-owned resources (e.g., `ReviewPolicy`, `BookmarkPolicy`).
- Prevent Insecure Direct Object References (IDOR): Always verify `$user->id === $review->user_id` before mutation.

## 3. Input Sanitization & File Uploads
- Never trust `$file->getClientOriginalName()`.
- Validate file extension, MIME type, and size on upload:
  ```php
  $request->validate([
      'pdf' => ['required', 'file', 'mimes:pdf', 'max:51200'],
  ]);
  ```
- Store PDFs in `storage/app/private_pdfs` outside the public web root.

## 4. Rate Limiting
- Apply `throttle:login` (5/min) on auth endpoints.
- Apply `throttle:downloads` (10/min) on PDF stream controllers.
