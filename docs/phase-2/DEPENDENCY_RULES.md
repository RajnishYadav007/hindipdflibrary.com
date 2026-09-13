# Phase 2: Dependency Injection & Architecture Rules

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Scope:** Inversion of Control, Service Boundaries, and Persistence Access  
**Status:** Accepted & Enforced  

---

## 1. Dependency Injection vs Laravel Facades

Laravel provides both constructor injection via its Service Container and expressive Facades. We balance architectural purity with Laravel idiomatic ergonomics.

### 1.1. When to Use Dependency Injection
- In **Actions** and **Services** requiring external services or third-party client contracts (e.g., injecting an image conversion interface or mail service).
- In **Controllers** when consuming domain Actions:
  ```php
  public function store(StoreBookRequest $request, CreateBookAction $createBook): RedirectResponse
  {
      $book = $createBook->handle($request->validated());
      return redirect()->route('books.show', $book);
  }
  ```

### 1.2. When Laravel Facades are Acceptable
- Built-in framework facades with standard contracts (`DB`, `Route`, `Schema`, `Cache`, `Storage`, `Log`, `Gate`) are completely acceptable in standard Laravel code.
- We do NOT wrap standard Laravel facades in artificial adapters merely to avoid using facades.

---

## 2. Action vs Service Decision Matrix

| Dimension | Use an Action (`app/Actions/`) | Use a Service (`app/Services/`) |
| :--- | :--- | :--- |
| **Granularity** | Single, focused task (e.g., `PublishBook`, `RecordDownload`) | Broad, multi-method subsystem (e.g., `SeoService`, `MediaService`) |
| **Methods** | Exactly one primary entry point (`handle()`) | Multiple related methods (`generateJsonLd()`, `buildHreflangTags()`) |
| **State** | Stateless command | Stateless or configured domain coordinator |
| **When to Avoid** | Do not create an action for basic single-line Eloquent creates (`User::create()`) | Do not create entity services (`UserService`, `CategoryService`) |

---

## 3. Eloquent vs Query Builder vs Repositories

### 3.1. When to Use Eloquent (Default)
- Standard relational operations (CRUD).
- Eager loading relationships (`Book::with(['author', 'category'])`).
- Model events and lifecycle hooks (`creating`, `saved`, `deleted`).
- Query scopes (`Book::published()->featured()`).

### 3.2. When to Use Query Builder (`DB::table(...)`)
- High-throughput batch inserts (e.g., bulk WordPress import migrations).
- Analytical aggregations across thousands of rows (e.g., summing daily pageviews).
- Raw performant subqueries where hydrating thousands of Eloquent model instances would cause excessive memory usage on shared hosting.

### 3.3. When NOT to Create Repositories
> [!IMPORTANT]
> **No Repository Pattern by Default:**
> In Laravel, Eloquent already implements both the ActiveRecord and Query Builder patterns. Adding a repository interface and implementation for every model (`BookRepositoryInterface` -> `BookRepository`) introduces two unnecessary layers of indirection with zero benefit for standard applications.
>
> We do NOT use repositories for this project. Use Eloquent, query scopes, and Actions directly.

---

## 4. Data Transfer Objects (DTOs) & Value Objects

### 4.1. When DTOs are Justified
- Complex multi-source input that needs strict typing across multiple subsystems (e.g., `WordPressImportData` containing posts, postmeta, terms, and attachments).
- External API payloads where strict schema contracts prevent runtime errors.

### 4.2. When DTOs are Forbidden (Over-Engineering)
- Standard HTTP web requests where Form Requests already provide typed, validated arrays (`$request->validated()`).
- Internal model-to-model transformations where Eloquent models or array primitives are clear and sufficient.
