# Phase 2: Application Architecture Decision Record (ADR)

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Scope:** Core Application Layers, Responsibilities, and Boundary Standards  
**Deciders:** Principal Software Architect, Staff Laravel Engineer  
**Status:** Accepted & Enforced  

---

## 1. Application Layers & Flow of Dependency

We enforce a strict three-tier, unidirectional architectural hierarchy:

```
[ PRESENTATION LAYER ]
  ├── HTTP Web Controllers (`app/Http/Controllers/`)
  ├── Livewire Public Components (`app/Livewire/`)
  ├── Filament Admin Panels (`app/Filament/`)
  └── Console Commands (`app/Console/Commands/`)
          │
          ▼
[ APPLICATION & DOMAIN LAYER ]
  ├── Form Requests / Validation (`app/Http/Requests/`)
  ├── Authorization Policies (`app/Policies/`)
  ├── Single-Purpose Actions (`app/Actions/`)
  ├── Cross-Cutting Domain Services (`app/Services/`)
  └── Reusable Support Utilities (`app/Support/`)
          │
          ▼
[ PERSISTENCE LAYER ]
  ├── Eloquent Models (`app/Models/`)
  ├── Query Scopes & Database Migrations (`database/migrations/`)
  └── MySQL 8 Storage Engine
```

### Strict Rule on Dependency Direction:
- **Presentation** calls **Application Actions/Services** or queries **Models** directly for read-only displays.
- **Actions and Services** interact with **Models and Database**.
- **Models** NEVER depend on HTTP Requests, Session state, or Livewire components.
- Layers never depend backwards (Persistence cannot know about Controllers or Blade).

---

## 2. Layer Responsibilities & Rules

### 2.1. Controllers (Thin Web Controllers)
- **Responsibility:** Ingest HTTP request, authorize via Policy, invoke Action/Service, return appropriate View or Redirect.
- **Forbidden in Controllers:**
  - Direct raw SQL queries or complex multi-table joins.
  - Multi-step business workflows (e.g., creating a book, converting cover image, generating slug, triggering notifications).
  - SEO meta tag generation algorithms.
  - File upload processing logic.

### 2.2. Actions (`app/Actions/`)
- **Responsibility:** Represent a discrete, single-purpose application operation (e.g., `CreateBook`, `PublishReview`, `RecordBookDownload`).
- **Characteristics:**
  - One public entry point (typically `handle()` or `execute()`).
  - Explicit typed parameters; returns an explicit typed result.
  - Testable in isolation without simulating HTTP requests.
  - Completely decoupled from `Illuminate\Http\Request` or UI state.

### 2.3. Services (`app/Services/`)
- **Responsibility:** Cohesive domain workflows or cross-cutting technical concerns requiring multiple steps or external integrations.
- **When to Use a Service:**
  - `app/Services/SEO/`: Orchestrating canonical URLs, alternate hreflang tags, and Schema.org JSON-LD generation.
  - `app/Services/Analytics/`: Aggregating raw pageview buffers into daily summary tables.
  - `app/Services/Media/`: Handling image resizing and WebP conversion pipelines.
- **Rule:** Do NOT create a service for every entity (`UserService`, `CategoryService`). Prefer Actions for discrete CRUD operations.

### 2.4. Authorization Policies (`app/Policies/`)
- **Responsibility:** Centralized authorization rules for user actions.
- **Rule:** Never rely solely on hidden buttons in Blade/Livewire for security. Every state-mutating endpoint must authorize through a Policy (`$this->authorize(...)` or `Gate::authorize(...)`).

### 2.5. Form Requests (`app/Http/Requests/`)
- **Responsibility:** Encapsulate validation rules and authorization checks for non-trivial HTTP endpoints.
- **Rule:** Simple GET filters or trivial endpoints may use `$request->validate()`, but any multi-field creation/update endpoint must use a dedicated Form Request class.

### 2.6. Models (`app/Models/`)
- **Responsibility:** Relational persistence, attribute casting, relationship definitions, local query scopes, and lightweight state checks.
- **Forbidden in Models:**
  - Giant "God Model" anti-patterns.
  - Email sending or PDF disk streaming logic.
  - Direct HTTP or session manipulation.

### 2.7. Livewire Components (`app/Livewire/`)
- **Responsibility:** Presentation-level reactive UI components for the public frontend (e.g., debounced search bar, bookmark toggle, review submission modal).
- **Rule:** Livewire components act like controllers. When a user action performs a business mutation, the Livewire component invokes an Action class.

### 2.8. Filament Admin (`app/Filament/`)
- **Responsibility:** Back-office administrative interface.
- **Rule:** Admin panel components remain strictly isolated in `app/Filament/`. Public frontend code must never import or depend on Filament packages or resources.

---

## 3. Database Access Architecture (No Fake Repositories)

- **Decision:** Use **Eloquent ORM + Laravel Query Builder** directly.
- **Justification:** Eloquent already implements an ActiveRecord and Query Builder pattern. Wrapping Eloquent models in boilerplate repositories (`BookRepositoryInterface`, `BookRepository`) produces an unnecessary layer of indirection that complicates maintenance without providing architectural benefit.
- **When to abstract:** If a query is complex or reused across multiple contexts, encapsulate it in a local model scope (`scopePublished()`, `scopeFeatured()`) or an Action class.

---

## 4. Route Organization & Naming Standards

- **Files:**
  - `routes/web.php`: Public guest and user account routes.
  - `routes/auth.php`: Authentication routes (login, registration, password recovery).
  - `routes/console.php`: Scheduled tasks and Artisan commands.
- **Conventions:**
  - URLs must use lowercase kebab-case: `/books/{slug}`, `/forgot-password`.
  - Every route must have an explicit name: `->name('home')`, `->name('books.show')`.
  - Zero hardcoded URL strings inside controllers, views, or Livewire components. Always use `route('name')`.

---

## 5. Testing Architecture

Tests reside in:
- `tests/Unit/`: Pure unit tests for standalone classes and helpers.
- `tests/Feature/`: HTTP integration tests testing controllers, Livewire components, authentication, and panel access.
- `tests/Architecture/`: Architectural guardrail tests ensuring layer rules, namespace standards, and security checks are never broken by future development.
