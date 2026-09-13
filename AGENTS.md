# AI Engineering Guidelines & Operational Standards (AGENTS.md)

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Application Type:** Digital Book/Library Discovery & Content Platform  
**Target Stack:** Laravel 13, PHP 8.4+, MySQL 8, Livewire 4, Filament 5, Tailwind CSS  
**Target Environment:** Shared Hosting (StackCP/ServerByt) V1 ➔ VPS/Cloud V2  

---

## 1. Mandatory Instructions for All AI Agents

> [!IMPORTANT]
> **EVERY AI coding agent working on this repository MUST read and adhere strictly to this file before creating, modifying, or executing any code or commands.**

### The 5 Cardinal Rules:
1. **INSPECT BEFORE IMPLEMENTING:** Always check existing files, schema, and tests before creating new ones.
2. **VERIFY BEFORE ASSUMING:** Never assume infrastructure capabilities (e.g., Redis, persistent workers, Meilisearch) exist unless confirmed in `docs/architecture/`.
3. **PRODUCTION SAFETY FIRST:** Never run destructive commands (`rm -rf`, `DROP DATABASE`, `migrate:fresh`, `git reset --hard`) without explicit, documented user authorization.
4. **NO OVER-ENGINEERING:** Prefer native Laravel conventions and built-in features over unnecessary third-party packages, complex microservices, or redundant repository abstractions.
5. **SEO & MULTILINGUAL FIRST:** Hindi PDF Library is an SEO-heavy digital library. Every public URL, slug, meta tag, and database query must respect English/Hindi multilingual SEO.

---

## 2. Architecture Principles & Layer Responsibilities

```
Presentation (Controllers, Livewire, Filament, Console)
    ↓
Application (Actions, Services, Policies, Form Requests)
    ↓
Domain / Persistence (Eloquent Models, Scopes, MySQL)
```

1. **Unidirectional Dependency:** Presentation calls Application Actions/Services; Actions/Services interact with Models and DB. Models never depend on HTTP, session, or Livewire state.
2. **Shared-Hosting Compatible V1:**
   - No daemon requirements (no Supervisor, no background workers running infinitely).
   - Queue jobs use `QUEUE_CONNECTION=database` with `--stop-when-empty` cron execution.
   - Cache uses `CACHE_STORE=file` or `CACHE_STORE=database` via Laravel's Cache facade. Zero direct `Redis::` calls.
   - Search uses MySQL 8 `FULLTEXT` natural language boolean queries in V1.
3. **Clean Abstractions Without Over-Engineering:**
   - Always code against standard Laravel interfaces (`Illuminate\Contracts\...`).
   - When VPS/Cloud migration occurs, changing `.env` must be sufficient with zero code refactoring.
4. **No Future Feature Creep Rule:**
   - NEVER implement future business features (e.g., books, authors, categories, reviews, analytics, sitemaps) ahead of the designated project phase. Implement ONLY what the user requests for the active phase.

---

## 3. Layer Coding & Engineering Standards

### 3.1. Controllers (Thin Controllers)
- Controllers must ONLY: accept request, authorize, call an Action or Service, and return a View or RedirectResponse.
- Controllers must NEVER: contain raw SQL, multi-step business pipelines, file processing, or SEO generation algorithms.

### 3.2. Actions (`app/Actions/`)
- Encapsulate a single, discrete business mutation (e.g., `CreateBook`, `PublishReview`).
- Must have one clear entry point (`handle()`), explicit typed inputs, and explicit return types.
- Completely decoupled from `Request`, `Session`, or Livewire component state.

### 3.3. Services (`app/Services/`)
- Reserved for cohesive, cross-cutting technical subsystems (e.g., `app/Services/SEO/`, `app/Services/Analytics/`, `app/Services/Media/`).
- Do NOT create a service for every model (`UserService`, `AuthorService` are prohibited). Use Actions for discrete operations.

### 3.4. Policies (`app/Policies/`)
- Centralized server-side authorization. Never rely solely on hidden UI buttons or Blade `@can` for security.
- Every state-modifying action must authorize via Policy (`$this->authorize(...)` or `Gate::authorize(...)`).

### 3.5. Form Requests (`app/Http/Requests/`)
- Encapsulate validation and authorization for non-trivial HTTP endpoints.
- Avoid large `$request->validate([...])` blocks inside controllers.

### 3.6. Models & Persistence (`app/Models/`)
- Eloquent Models handle relational persistence, casts, relationships, and local query scopes.
- Models must NEVER become God Classes containing email dispatch, PDF streaming, or HTTP responses.
- **NO REPOSITORIES:** Do NOT write `BookRepository` or `UserRepository`. Use Eloquent and Laravel Query Builder directly.

### 3.7. Livewire Components (`app/Livewire/`)
- Livewire components are presentation-layer UI boundaries for the public site.
- Components must delegate business mutations to Action classes, never writing business logic directly in component methods.

### 3.8. Filament Admin (`app/Filament/`)
- Filament 5 is strictly for administrative back-office functionality (`/admin`).
- Public frontend code must never import or depend on Filament resources.
- Access to the panel is protected by `FilamentUser` contract requiring `is_admin === true`.

### 3.9. Routes & Naming Standards
- Standard route files: `routes/web.php`, `routes/auth.php`, `routes/console.php`.
- URLs: clean, lowercase kebab-case (`/forgot-password`).
- Route Names: every route must have an explicit lowercase name (`route('home')`, `route('login')`).
- Never hardcode URLs in controllers, views, or Livewire components.

### 3.10. Dependency Injection & Configuration
- Prefer constructor or method injection over global static helpers.
- Laravel facades are acceptable and idiomatic where appropriate.
- Access environment configuration via `config('app.name')`. Never call `env(...)` in application classes (returns `null` when config is cached).

---

## 4. Migration Rules

1. **Non-Destructive Migrations:**
   - Migrations in production must **NEVER** drop columns or tables without a staged deprecation plan.
   - `migrate:fresh` is strictly forbidden in production or staging.
2. **Character Set & Collation:**
   - Every text column supporting Hindi must use `utf8mb4` with `utf8mb4_unicode_ci` or `utf8mb4_0900_ai_ci`.
3. **Rollback Integrity:**
   - Every `up()` method must have a clean, reversible `down()` method.

---

## 5. Security & Input Validation Rules

1. **Authentication & Authorization:**
   - Every controller and Livewire action modifying data must authorize via Policy or Spatie permission check (`$this->authorize(...)`).
2. **Raw Queries Forbidden:**
   - Direct SQL interpolation (`DB::raw("WHERE title = '$search'")`) is strictly prohibited. Use PDO parameter binding.
3. **File Upload Hardening:**
   - Always validate MIME types via PHP `finfo` magic bytes (`application/pdf`, `image/webp`).
   - Always generate cryptographic UUID filenames. Never trust client-supplied filenames.
   - Private PDFs must be stored in `storage/app/private_pdfs/` outside public web root.

---

## 6. SEO & Performance Rules

1. **URLs & Slugs:**
   - Slugs must be clean, lowercase, hyphen-delimited ASCII strings (transliterated from Hindi if needed).
   - URLs must be canonicalized with proper `<link rel="canonical">` and `hreflang` tags.
2. **Structured Data:**
   - Every book page must output Schema.org `Book` JSON-LD markup including: title, author, isbn, numberOfPages, inLanguage, and aggregateRating.
3. **N+1 Query Prevention:**
   - Always eager-load relationships (`Book::with(['author', 'category', 'media'])`).
   - Enable `Model::preventLazyLoading(! app()->isProduction())` in local development.

---

## 7. Package & Dependency Rules

1. **Approval Required:**
   - Do not install new Composer or NPM packages without documenting rationale in `docs/architecture/PACKAGE_COMPATIBILITY_MATRIX.md`.
2. **No Nulled / Abandoned Packages:**
   - Only official, MIT/BSD-licensed, actively maintained packages are permitted.

---

## 8. Definition of Done (DoD)

Before declaring any feature or workstream complete:
1. Code follows PSR-12 formatting (`php artisan pint` or `vendor/bin/pint`).
2. Automated tests pass (`php artisan test`).
3. Architecture tests pass (`php artisan test --testsuite=Architecture`).
4. Documentation updated in `docs/`.
5. No console errors or unresolved PHP deprecation warnings.
6. Production safety verified (no secrets committed, no hardcoded URLs).
