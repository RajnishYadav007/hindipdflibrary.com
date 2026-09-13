# Phase 4 RBAC & Authorization Architecture

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Package:** Spatie Laravel Permission (`v8.3.0`)  
**Target:** Laravel 13  

---

## 1. Role Matrix & Functional Scopes

| Role Name | Scope & Purpose | Assigned Permissions |
| :--- | :--- | :--- |
| **Super Admin** | Full root access across all features, settings, and infrastructure. | Bypasses all gate checks via `Gate::before` in `AppServiceProvider`. |
| **Admin** | Full back-office management for operational staff. | All 30 permissions across Books, Authors, Categories, Genres, Languages, Reviews, Users, Media, SEO, Redirects. |
| **Editor** | Content curation, catalog creation, digital asset metadata, and SEO rules. | `books.*`, `authors.*`, `categories.*`, `genres.*`, `languages.*`, `media.view`, `media.create`, `media.update`, `seo.*`, `redirects.*`. Blocked from `users.*` and `reviews.*`. |
| **Moderator** | Community content review and triage. | `reviews.view`, `reviews.update`, `reviews.delete`. Blocked from modifying books, authors, users, or system settings. |

---

## 2. Granular Permissions Inventory

| Domain | Permission Slugs |
| :--- | :--- |
| **Books** | `books.view`, `books.create`, `books.update`, `books.delete` |
| **Authors** | `authors.view`, `authors.create`, `authors.update`, `authors.delete` |
| **Categories** | `categories.view`, `categories.create`, `categories.update`, `categories.delete` |
| **Genres** | `genres.view`, `genres.create`, `genres.update`, `genres.delete` |
| **Languages** | `languages.view`, `languages.create`, `languages.update`, `languages.delete` |
| **Reviews** | `reviews.view`, `reviews.update`, `reviews.delete` |
| **Users** | `users.view`, `users.create`, `users.update`, `users.delete` |
| **Media** | `media.view`, `media.create`, `media.update`, `media.delete` |
| **SEO Directives**| `seo.view`, `seo.create`, `seo.update`, `seo.delete` |
| **Redirects** | `redirects.view`, `redirects.create`, `redirects.update`, `redirects.delete` |

---

## 3. Policy Enforcement Layer

All authorization checks are performed on the server side inside `app/Policies/`:
1. `BookPolicy.php`
2. `AuthorPolicy.php`
3. `CategoryPolicy.php`
4. `GenrePolicy.php`
5. `LanguagePolicy.php`
6. `ReviewPolicy.php`
7. `UserPolicy.php`
8. `MediaPolicy.php`
9. `SeoMetaPolicy.php`
10. `RedirectPolicy.php`

### Super Admin Gate Bypass
In `app/Providers/AppServiceProvider.php`:
```php
Gate::before(function ($user, string $ability) {
    return $user->hasRole('Super Admin') ? true : null;
});
```
This guarantees that Super Admins can never be locked out of administrative functions even if individual permissions are adjusted.

---

## 4. Default Seeded Accounts (Development / Staging)

| Role | Email | Default Password | Access Level |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `superadmin@example.com` | `Password123!` | Global root access |
| **Admin** | `admin@example.com` | `Password123!` | All CMS operations |
| **Editor** | `editor@example.com` | `Password123!` | Content & SEO management |
| **Moderator** | `moderator@example.com` | `Password123!` | Review moderation queue |

> [!CAUTION]
> Production deployments must run with randomized secure credentials and remove sample test accounts.
