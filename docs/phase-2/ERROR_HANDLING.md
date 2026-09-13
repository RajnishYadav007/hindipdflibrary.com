# Phase 2: Error & Exception Handling Architecture

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Scope:** Framework Exception Handling, HTTP Responses, and Logging Standards  
**Status:** Accepted & Enforced  

---

## 1. Executive Summary

A robust error handling architecture ensures that unexpected runtime exceptions never leak sensitive database credentials, query strings, or stack traces to end-users, while logging rich operational diagnostics for engineers.

In accordance with our core principle (**no over-engineering**), we rely on native Laravel 13 exception handling conventions rather than generating dozens of redundant custom exception classes.

---

## 2. Exception Hierarchy & Handling Matrix

| Exception Class | Trigger Context | HTTP Status | User-Facing Behavior | Logging Level |
| :--- | :--- | :--- | :--- | :--- |
| **`ModelNotFoundException`** / `NotFoundHttpException` | Resource or URL not found | `404 Not Found` | Renders `errors/404.blade.php` | `notice` (Filtered from critical alerts) |
| **`AccessDeniedHttpException`** / `AuthorizationException` | Policy or Gate failure | `403 Forbidden` | Renders `errors/403.blade.php` | `warning` |
| **`ValidationException`** | Form Request or validator failure | `422 Unprocessable` | Flashed to session; renders validation errors in form | Do not log (Normal user validation flow) |
| **`AuthenticationException`** | Unauthenticated user accessing auth route | `401 Unauthorized` / `302 Redirect` | Redirects to `route('login')` with intended URL | Do not log |
| **`TokenMismatchException`** | Expired CSRF token | `419 Page Expired` | Renders `errors/419.blade.php` | `notice` |
| **`ThrottleRequestsException`** | Rate limit exceeded | `429 Too Many Requests` | Renders `errors/429.blade.php` with Retry-After | `warning` |
| **`QueryException`** / `PDOException` | Database query or connection error | `500 Server Error` | Renders `errors/500.blade.php` (Zero SQL leakage) | `critical` |
| **Unhandled `Throwable`** | General uncaught exception | `500 Server Error` | Generic error page in production | `error` |

---

## 3. Production vs Local Error Display

- **`APP_DEBUG=true` (Local Development Only):**
  Detailed error pages displaying stack traces, route bindings, and SQL query parameters (powered by Collision / Whoops).
- **`APP_DEBUG=false` (Production Enforced):**
  Never display stack traces. Every unhandled 500 error renders our custom branded view `resources/views/errors/500.blade.php`.
- **API / Livewire Requests:**
  JSON responses return standard `{ "message": "Server Error" }` payloads without internal PHP exception traces.

---

## 4. Custom Application Exceptions Guidelines

Custom exceptions should only be created when they represent an actionable **domain boundary condition** that requires distinct handling or specialized logging.

### Example Future Pattern:
```php
namespace App\Exceptions;

use Exception;

class BookNotDownloadableException extends Exception
{
    // Custom domain exception thrown when copyright or license forbids download
}
```

### Prohibited Anti-Patterns:
1. **Empty Catch Blocks:** Catching exceptions without logging or re-throwing (`catch (Exception $e) {}`).
2. **Generic `dd()` / `dump()`:** Debugging statements must never be committed to production branches.
3. **Leaking Raw SQL:** Never display `$e->getMessage()` directly to users when catching `PDOException` or `QueryException`.
