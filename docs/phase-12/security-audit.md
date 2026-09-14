# Phase 12 Security Audit & Hardening Matrix

## 1. Threat Model & Architecture
Hindi PDF Library serves public readers, registered members, and back-office administrators. Digital library assets require strict access control, isolation from direct web access, and protection against unauthorized scraping, bandwidth exhaustion, and injection attacks.

---

## 2. Private PDF Isolation Architecture
- **Location:** `storage/app/private_pdfs/` (strictly outside `public/` web root).
- **Public Storage Symlink Boundary:** The public storage symlink (`php artisan storage:link`) only points `public/storage` to `storage/app/public`. The `private_pdfs` directory has no symlink and cannot be traversed or accessed by direct web server URLs (`/storage/private_pdfs/...` returns 403/404).
- **Controller-Enforced Delivery:** All PDF downloads flow exclusively through `BookController@download`.
- **MIME & Magic Bytes Verification:** Prior to streaming, files are validated with `finfo_file($finfo, $path)` ensuring magic bytes match `application/pdf`. Non-PDF files or polyglot exploits trigger immediate `415 Unsupported Media Type` termination.
- **Header Hardening on Streams:**
  - `Content-Type: application/pdf`
  - `Content-Disposition: attachment; filename="[sanitized].pdf"`
  - `X-Content-Type-Options: nosniff`

---

## 3. Rate Limiting Matrix
Rate limiters configured in `AppServiceProvider`:

| Limiter Key | Throttle Limit | Scope / Key | Protected Endpoints | Action on Exceeded |
| :--- | :--- | :--- | :--- | :--- |
| `downloads` | 30 req / min | User ID or IP | `/book/{slug}/download`, `/books/{slug}/download` | HTTP 429 Too Many Requests |
| `search` | 60 req / min | User ID or IP | `/{locale}/search`, `/search` | HTTP 429 Too Many Requests |
| `reviews` | 10 req / min | User ID or IP | Review & rating submission forms | HTTP 429 Too Many Requests |
| `auth` | 10 req / min | IP address | Login, registration, password reset endpoints | HTTP 429 Too Many Requests |

---

## 4. HTTP Security Headers
Set globally across all responses via `App\Http\Middleware\SecurityHeaders`:

```http
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()
Strict-Transport-Security: max-age=31536000; includeSubDomains (Production only)
```

---

## 5. SSRF & Source URL Sanitization
External reading links (`source_url`) pass through `App\Support\Security\SafeUrl`:
- **Allowed Protocols:** Strictly `http://` and `https://`.
- **Blocked Protocols:** `javascript:`, `data:`, `file:`, `phar:`, `php:`, `view-source:`, `blob:`.
- **Blocked Hostnames & IPs:**
  - Loopback: `localhost`, `127.0.0.1`, `::1`, `0.0.0.0`.
  - Cloud Metadata / Link-Local: `169.254.169.254`.
  - Private IP Ranges: Filtered via `FILTER_FLAG_NO_PRIV_RANGE` and `FILTER_FLAG_NO_RES_RANGE`.

---

## 6. Audit Verification Summary
- Automated test coverage in `tests/Feature/SecurityAndLegal/`:
  - `LegalAndRightsTest`: Validates copyright rules, external links, safe source redirection.
  - `PrivatePdfStorageTest`: Validates private disk isolation, draft protection, MIME validation.
  - `SecurityHeadersAndRedirectsTest`: Validates header injection and 301/302 execution.
  - `RateLimitingTest`: Validates 429 enforcement under load.
