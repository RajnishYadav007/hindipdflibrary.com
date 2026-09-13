# Phase 4 Architecture Decision Records (ADRs)

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Phase:** Phase 4 — Admin Panel / CMS  

---

## ADR 01: Filament 5 Modular Architecture Adoption

- **Context:** Filament 5 introduces unified `Filament\Schemas\Schema` constructs for layout components (`Section`, `Tabs`, `Grid`, `Group`) and granular table configurations.
- **Decision:** Adopt modular resource directories:
  - `app/Filament/Resources/{Entity}/{Entity}Resource.php`
  - `app/Filament/Resources/{Entity}/Schemas/{Entity}Form.php`
  - `app/Filament/Resources/{Entity}/Tables/{Entity}Table.php`
  - `app/Filament/Resources/{Entity}/Pages/*`
- **Consequences:** Provides extreme modularity, clean separation of concern between form schemas and table configurations, and guarantees high testability and readability.

---

## ADR 02: Spatie Laravel Permission (`v8.3.0`) for RBAC

- **Context:** An enterprise CMS requires granular roles (Super Admin, Admin, Editor, Moderator) with server-side policy enforcement.
- **Decision:** Install `spatie/laravel-permission` v8.3.0, registered in `AppServiceProvider` with a Super Admin `Gate::before` bypass.
- **Consequences:** Zero custom permission glue code needed. Built-in caching avoids redundant database queries on shared hosting.

---

## ADR 03: Direct Eloquent Usage (No Repository Pattern)

- **Context:** Some architectures wrap Eloquent in redundant repository abstractions.
- **Decision:** As mandated by project rules, reject the repository pattern. Filament resources and Laravel Policies interact directly with Eloquent models.
- **Consequences:** Eliminates boilerplate interfaces, avoids maintenance debt, and utilizes full Laravel Eloquent query builder features natively.

---

## ADR 04: Real Database Metric Widgets (No Mock Charts)

- **Context:** Admin dashboards frequently implement fake charts or hardcoded numbers during MVP phases.
- **Decision:** `StatsOverviewWidget` computes live counts directly against MySQL database tables (`books`, `authors`, `categories`, `reviews`, `users`).
- **Consequences:** The administrative dashboard displays real, reliable library statistics from day one.
