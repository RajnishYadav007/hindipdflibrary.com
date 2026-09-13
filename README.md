# Hindi PDF Library (`hindipdflibrary.com`)

Modern, high-performance digital book and literature discovery platform migrating from WordPress to a custom Laravel 13 architecture.

---

## 1. Technology Stack
- **Backend:** Laravel 13 (`laravel/framework` v13.31.0)
- **PHP:** PHP 8.4+ (8.5.8 local CLI, 8.4.25 production Apache)
- **Database:** MySQL 8.0+ / MariaDB (`utf8mb4_unicode_ci` for full Hindi Devanagari script support)
- **Frontend:** Blade + Livewire 4 (`livewire/livewire` v4.4.4) + Tailwind CSS 3
- **Administration:** Filament 5 (`filament/filament` v5.8.1)
- **Authentication:** Laravel Breeze with Livewire stack
- **Build Tool:** Vite v7.3.6

---

## 2. Architecture & Documentation
Detailed architectural blueprints and decision records reside in `docs/`:
- **Phase 0 Baseline:** [`docs/PHASE_0_FINAL_REPORT.md`](docs/PHASE_0_FINAL_REPORT.md) and [`docs/architecture/`](docs/architecture/)
- **Phase 1 Foundation:** [`docs/phase-1/PHASE_1_IMPLEMENTATION.md`](docs/phase-1/PHASE_1_IMPLEMENTATION.md)
- **Phase 2 Architecture:** [`docs/phase-2/`](docs/phase-2/)
  - [Architecture Baseline](docs/phase-2/ARCHITECTURE_BASELINE.md)
  - [Architecture Decisions & Layers](docs/phase-2/ARCHITECTURE_DECISION.md)
  - [Dependency Injection & Persistence Rules](docs/phase-2/DEPENDENCY_RULES.md)
  - [Error & Exception Handling](docs/phase-2/ERROR_HANDLING.md)
  - [Testing Architecture](docs/phase-2/TESTING_ARCHITECTURE.md)
  - [Architectural Guardrails](docs/phase-2/ARCHITECTURAL_GUARDRAILS.md)
- **AI Developer Standards:** [`AGENTS.md`](AGENTS.md) (Mandatory reading for human and AI engineers)

---

## 3. Local Development Setup

### 3.1. Prerequisites
- PHP 8.4 or higher with `pdo_mysql`, `mbstring`, `intl`, and `gd` extensions enabled.
- Composer 2.2+
- Node.js 18+ and NPM 9+
- MySQL 8.0+ / MariaDB service running on `127.0.0.1:3306`

### 3.2. Installation Steps
```bash
# 1. Clone the repository
git clone <repository-url> hindipdflibrary
cd hindipdflibrary

# 2. Install PHP dependencies
composer install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Configure local database credentials in .env and run migrations
php artisan migrate

# 5. Install and compile frontend assets
npm install
npm run build
```

---

## 4. Development & Testing Commands

```bash
# Run local development server
php artisan serve

# Run Vite asset compiler in watch mode
npm run dev

# Compile assets for production
npm run build

# Run all automated test suites (Unit, Feature, Architecture)
php artisan test

# Run only architectural boundary enforcement tests
php artisan test --testsuite=Architecture

# Inspect application routing
php artisan route:list
```

---

## 5. Administration Access
- The administration panel is located at `/admin`.
- Panel access is governed by the `FilamentUser` contract on `App\Models\User` and strictly requires `is_admin === true`.
