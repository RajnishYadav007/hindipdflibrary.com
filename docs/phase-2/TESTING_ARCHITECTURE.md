# Phase 2: Testing Architecture & Quality Assurance

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Scope:** Test Organization, Automation Standards, and Architecture Guards  
**Status:** Accepted & Enforced  

---

## 1. Test Suite Organization

The testing infrastructure is structured into three dedicated suites in `phpunit.xml`:

```
tests/
├── Unit/                  <-- Pure isolated component & helper tests
│   └── ExampleTest.php
│
├── Feature/               <-- HTTP, Livewire, Form Requests, and Auth integration tests
│   ├── AdminAccessTest.php
│   └── Auth/
│       ├── AuthenticationTest.php
│       ├── EmailVerificationTest.php
│       ├── PasswordConfirmationTest.php
│       ├── PasswordResetTest.php
│       ├── PasswordUpdateTest.php
│       └── RegistrationTest.php
│
└── Architecture/          <-- Automated architectural rule enforcement
    └── ArchitectureTest.php
```

---

## 2. Test Execution & Performance Standards

- **In-Memory SQLite Database:** Tests run using `DB_CONNECTION=sqlite` with `:memory:`. No disk writes or shared database locks occur during test runs.
- **Speed Objective:** Unit and Architecture tests must complete in $< 2\text{s}$. The full feature test suite must complete in $< 45\text{s}$.
- **RefreshDatabase Trait:** Feature tests mutating data must use `Illuminate\Foundation\Testing\RefreshDatabase` to ensure zero state pollution between test cases.

---

## 3. Architecture Testing Rules (`tests/Architecture/`)

Automated architectural tests in `tests/Architecture/ArchitectureTest.php` enforce the following rules on every commit:
1. **Directory Integrity:** Verifies that required architectural folders (`app/Actions`, `app/Services`, `app/Policies`, `app/Support`, `app/Http/Controllers`, `app/Livewire`, `app/Models`) exist.
2. **Model Inheritance:** Asserts that all classes in `app/Models/` extend `Illuminate\Database\Eloquent\Model` (or Authenticatable) and can be autoloaded cleanly.
3. **Debug Code Prevention:** Recursively scans the entire `app/` codebase to assert that raw debugging functions (`dd(`, `dump(`, `var_dump(`) are never committed.
4. **Admin Protection Guard:** Verifies that `/admin` endpoints automatically reject unauthenticated guests and redirect them to `/admin/login`.

---

## 4. Running Tests

```bash
# Run all test suites
php artisan test

# Run only architectural boundary tests
php artisan test --testsuite=Architecture

# Run only feature tests
php artisan test --testsuite=Feature
```
