# Workstream L: Security Baseline & Hardening Architecture

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Scope:** Application Security, Server Hardening, and Compliance  
**Auditor:** Lead Security Engineer & Solutions Architect  

---

## 1. Executive Summary

Security must be built into the architectural foundation prior to writing feature code. This baseline establishes non-negotiable security controls across:
1. Environment and secrets governance.
2. HTTP transport and edge proxy protection.
3. Authentication, session, and authorization hygiene.
4. Input sanitization, file upload defense, and injection mitigation.
5. Admin surface hardening and auditability.

---

## 2. Core Security Controls & Verification Matrix

| Domain | Security Control | Production Implementation | Verification Standard |
| :--- | :--- | :--- | :--- |
| **Transport** | HTTPS Enforcement | Force SSL via Apache `.htaccess` & `AppServiceProvider` | HTTP strictly 301 redirects to HTTPS; HSTS header active |
| **Environment** | `APP_DEBUG` | `APP_DEBUG=false` strictly enforced in production | No stack traces or environment variables shown on 500 error |
| **Environment** | `APP_ENV` | `APP_ENV=production` | Framework runs in optimized mode |
| **Key Management** | `APP_KEY` | 32-byte AES-256 key (`base64:...`) | Unique per environment; never committed to Git |
| **File Exposure** | `.env` Protection | Placed outside web root; blocked in Apache | Direct `curl https://hindipdflibrary.com/.env` returns 403/404 |
| **File Perms** | Filesystem Access | Code: `644`/`755`, Storage: `775` | No world-writable directories (`777` strictly forbidden) |
| **Uploads** | PDF & Image Checks | MIME magic bytes verification + UUID renaming | No executable files (`.php`, `.phtml`, `.sh`) allowed in storage |
| **Authentication** | Password Security | Bcrypt (cost 12) or Argon2id | Enforce minimum 8 chars, mixed case, numbers |
| **Session** | Session Cookie Config | `secure=true`, `http_only=true`, `same_site=lax` | Prevents cookie theft via XSS; 120-minute lifetime |
| **CSRF** | CSRF Tokens | Standard Laravel `VerifyCsrfToken` middleware | Active on all POST/PUT/PATCH/DELETE endpoints |
| **XSS Defense** | Output Escaping | Blade `{{ $variable }}` automatic escaping | Raw `{!! $var !!}` forbidden without explicit HTMLPurifier sanitization |
| **SQL Injection**| Prepared Statements | Eloquent ORM & PDO bindings exclusively | Raw SQL string interpolation (`DB::raw("...$id")`) strictly prohibited |
| **Rate Limiting** | Abuse Throttling | Laravel `RateLimiter` configured per IP / User | Login: 5/min, Search: 30/min, PDF Download: 10/min |
| **Admin Shield** | Filament Admin URL | Non-standard admin slug (e.g., `/portal-control-hpl/`) | Prevents automated brute-force attacks on `/admin` |
| **RBAC** | Role Authorization | Spatie Permission + Laravel Policies | Explicit checks (`can('manage-books')`) on every controller |

---

## 3. Detailed Hardening Specifications

### 3.1. Apache Web Server Hardening (`public/.htaccess`)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # 1. Force HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # 2. Block sensitive files and hidden dotfiles
    RewriteRule (^|/)\.(?!well-known) - [F]
    RewriteRule \.(env|json|lock|git|yml|yaml|sql|md)$ - [F]
</IfModule>

<IfModule mod_headers.c>
    # 3. Security Headers
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
    Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"
</IfModule>
```

### 3.2. Rate Limiting Architecture (`app/Providers/AppServiceProvider.php`)
```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

public function boot(): void
{
    // Throttles authentication attempts
    RateLimiter::for('login', function (Request $request) {
        return Limit::perMinute(5)->by($request->input('email') . '|' . $request->ip());
    });

    // Throttles public book searches to prevent scraping
    RateLimiter::for('search', function (Request $request) {
        return Limit::perMinute(30)->by($request->ip());
    });

    // Throttles PDF downloads to protect bandwidth
    RateLimiter::for('downloads', function (Request $request) {
        return Limit::perMinute(10)->by($request->ip());
    });
}
```

### 3.3. File Upload Defensive Architecture
1. **Magic Byte Inspection:** Rely on `Illuminate\Http\UploadedFile::getMimeType()`, which inspects PHP `finfo` magic byte headers, never trusting the client's `Content-Type`.
2. **Double-Extension Neutralization:** Discard original filename entirely. Store as `Str::uuid() . '.pdf'`.
3. **Execution Prevention:** The private PDF directory (`storage/app/private_pdfs/`) has an internal `.htaccess` or resides above the web root, disabling PHP execution entirely.

---

## 4. Admin Surface Security
- **Filament Admin Panel URL:** Do not expose the administration dashboard at `/admin`. Use an unguessable route configured in `app/Providers/Filament/AdminPanelProvider.php`.
- **Two-Factor Authentication (2FA):** Mandatory for all admin accounts before publication privileges are granted.
- **Admin Session Timeout:** Shortened to 30 minutes of inactivity.
