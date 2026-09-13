# Workstream E: Package Compatibility Matrix

**Date:** September 13, 2026  
**Target Framework:** Laravel 13  
**Auditor:** Lead Solutions Architect & Senior Laravel Engineer  

---

## 1. Executive Summary

Every proposed package in the technology roadmap was audited against:
1. Live Packagist package metadata (versions, dependencies, release dates).
2. Laravel 13 (`laravel/framework` v13.31.0) compatibility.
3. PHP 8.4+ compatibility.
4. Shared hosting operational constraints (memory footprints, absence of daemons).

**All proposed packages are verified compatible with Laravel 13 and suitable for shared hosting V1 deployment.**

---

## 2. Comprehensive Package Audit Matrix

| Package Name | Official Source / Vendor | Latest Version | PHP Requirement | Laravel Compatibility | Shared Hosting Fit | Status / License | Recommendation |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **`laravel/breeze`** | Laravel Official | `v2.4.2` | `^8.2` | `^11 \| ^12 \| ^13` | **Excellent** (Lightweight) | MIT / Active | **RECOMMENDED (V1 Auth)** |
| **`laravel/fortify`** | Laravel Official | `v1.39.0` | `^8.2` | `^11 \| ^12 \| ^13` | **Excellent** (Headless auth) | MIT / Active | **OPTIONAL** (Alternative to Breeze) |
| **`livewire/livewire`**| Caleb Porzio / Livewire | `v4.4.4` | `^8.1` | `^10 \| ^11 \| ^12 \| ^13`| **Excellent** (SSR reactive) | MIT / Active | **REQUIRED (Public Reactive UI)** |
| **`filament/filament`**| Filament Team | `v5.8.1` | `^8.2` | Laravel 13 Compatible | **Good** (Ensure OPcache on) | MIT / Active | **REQUIRED (Admin Panel)** |
| **`laravel/socialite`**| Laravel Official | `v5.31.0` | `^8.1` | `^6.0` through `^13.0` | **Excellent** (HTTP API) | MIT / Active | **REQUIRED (Google OAuth)** |
| **`spatie/laravel-permission`** | Spatie | `8.3.0` | `^8.3` | Laravel 13 Compatible | **Excellent** (Cached DB) | MIT / Active | **REQUIRED (RBAC Authorization)** |
| **`spatie/laravel-medialibrary`** | Spatie | `11.23.7` | `^8.2` | `^10.2` through `^13.0` | **Good** (Requires GD) | MIT / Active | **REQUIRED (Media Management)** |
| **`spatie/laravel-sitemap`** | Spatie | `8.2.0` | `^8.4` | `^12.0 \| ^13.0` | **Good** (Generate via Cron)| MIT / Active | **REQUIRED (SEO Sitemaps)** |
| **`laravel/boost`** | Laravel Official | `v2.8.1` | `^8.2` | `^11 \| ^12 \| ^13` | **Dev-Only** | MIT / Active | **RECOMMENDED (Dev Tooling)** |
| **`spatie/laravel-translatable`** | Spatie | `6.14.1` | `^8.3` | `^11 \| ^12 \| ^13` | **Excellent** (JSON columns)| MIT / Active | **RECOMMENDED (Multilingual)** |
| **`astrotomic/laravel-translatable`** | Astrotomic | `v11.17.1`| `^8.0` | `^9.0` through `^13.0` | **Good** (Join tables) | MIT / Active | **ALTERNATIVE (Multilingual)** |

---

## 3. In-Depth Package Rationale & Architecture Decisions

### 3.1. Authentication: `laravel/breeze` vs `laravel/fortify`
- **Decision:** Use **`laravel/breeze`** with Blade + Livewire scaffolding.
- **Rationale:** Breeze provides clean, transparent Blade templates, standard controller logic, password reset, and email verification out of the box. It does not hide authentication flow behind heavy abstractions, allowing easy customization for Google Socialite OAuth and dual-language login screens.

### 3.2. Public Frontend: `livewire/livewire` 4
- **Decision:** Approved.
- **Rationale:** Livewire 4 enables dynamic search filters, bookmarking/favorites toggles, review submissions, and pagination without requiring a separate Vue/React build step or client-side SPA routing. All rendering occurs server-side in PHP, which ensures maximum Google SEO indexability.

### 3.3. Administration: `filament/filament` 5
- **Decision:** Approved.
- **Rationale:** Filament 5 provides a world-class, highly productive administration panel built directly on Livewire and Tailwind CSS. It integrates natively with Spatie Permission and Spatie Media Library, saving hundreds of engineering hours on CMS scaffolding.
- **Shared Hosting Constraint:** Filament generates compiled Blade components. With Zend OPcache enabled (verified active in our environment), execution overhead is minimal.

### 3.4. Multilingual Model Strategy: `spatie/laravel-translatable` vs Native JSON
- **Decision:** **`spatie/laravel-translatable`**.
- **Rationale:** It stores translations in native MySQL 8 JSON columns (e.g., `{"en": "Godaan", "hi": "गोदान"}`) on the main entity table. This avoids messy relational joins across 10+ translation tables for books, genres, and authors, simplifying queries and maintaining rapid response times on shared MySQL.

### 3.5. Media Processing: `spatie/laravel-medialibrary`
- **Decision:** Approved with GD driver.
- **Rationale:** Since `ext-gd` is verified active in both local and shared environments while `imagick` is unverified, Medialibrary must be configured to use `gd` via `intervention/image`. Thumbnail generation should be offloaded to queues or sized conservatively to prevent shared hosting memory limit breaches.

### 3.6. SEO Automation: `spatie/laravel-sitemap`
- **Decision:** Approved for scheduled batch execution.
- **Rationale:** Sitemaps must never be computed dynamically on HTTP request arrival. Sitemaps will be generated to static XML files (`public/sitemap.xml`) via a scheduled Artisan command run during low-traffic periods.

---

## 4. Packages Explicitly Excluded (No Over-Engineering)

1. **`laravel/octane`:** Excluded. Octane requires persistent Swoole or RoadRunner CLI workers, which cannot run on shared Apache hosting.
2. **`meilisearch/meilisearch-php` & `laravel/scout` (Meilisearch engine):** Excluded for V1. Cannot run external search daemons on shared hosting. MySQL FULLTEXT indexing will be utilized natively.
3. **`predis/predis`:** Deferred. Not installed until hosting Redis support is verified.
4. **Heavy SPA Packages (Inertia/Vue/React):** Excluded. Blade + Livewire provides superior SEO indexability for a digital library while remaining shared-hosting lightweight.
