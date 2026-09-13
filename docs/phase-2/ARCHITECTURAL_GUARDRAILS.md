# Phase 2: Architectural Guardrails & Code Standards

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Scope:** Strict Engineering Guardrails for Human and AI Engineers  
**Status:** Mandatory  

---

## 1. The 10 Commandments of Application Architecture

### What You MUST DO:
1. **Keep Controllers Thin:** Limit controllers strictly to receiving requests, authorizing, calling Actions/Services, and returning responses.
2. **Use Single-Purpose Actions:** Encapsulate non-trivial domain write operations in `app/Actions/` with a single entry point (`handle()`).
3. **Use Cohesive Services:** Reserve `app/Services/` for cross-cutting subsystems (SEO generation, image processing, analytics aggregation).
4. **Use Server-Side Policies:** Authorize all state-modifying actions via Laravel Policies or Spatie Permission gates (`$this->authorize(...)`).
5. **Use Form Requests:** Validate multi-field creation/update endpoints using dedicated Form Request classes in `app/Http/Requests/`.
6. **Use Eloquent Directly:** Leverage Eloquent relationships, query scopes, and model casts without wrapping them in unnecessary repository layers.
7. **Use Explicit Named Routes:** Every route must have a unique, lowercase name (`route('home')`, `route('books.show')`). Never hardcode raw URLs in views.
8. **Eager Load Relationships:** Always prevent N+1 queries by eager-loading relations (`Book::with(['author', 'category'])`).
9. **Use Configuration via `config()`:** Read environment variables only in `config/*.php`. Code must access values via `config('app.name')`, never `env('APP_NAME')`.
10. **Test Core Behaviors:** Every Action, Policy, and Controller endpoint must have automated feature tests validating both success and forbidden/unauthorized flows.

---

### What You MUST NEVER DO (Prohibitions):
1. **DO NOT Put Business Logic in Controllers:** Never write raw database inserts, complex calculations, or multi-step pipelines in controller methods.
2. **DO NOT Put Business Logic in Blade Views:** Never run queries or authorization-bypassing mutations inside Blade templates.
3. **DO NOT Put Heavy Logic in Livewire Components:** Livewire components are presentation boundaries. When handling mutations, invoke an Action class.
4. **DO NOT Create Fake Repositories:** Never introduce `BookRepository`, `UserRepository`, etc. Eloquent is already our repository and active record.
5. **DO NOT Create a Service for Every Model:** Do NOT create `CategoryService`, `AuthorService`, or generic CRUD services.
6. **DO NOT Create Dumping-Ground Helpers:** Never create `Helpers.php` with random, unrelated global functions. Use dedicated `app/Support/` utility classes.
7. **DO NOT Hardcode Credentials or Secrets:** Never commit passwords, API keys, or tokens to version control.
8. **DO NOT Access `env()` Outside Config:** Calling `env(...)` in application code returns `null` when configuration is cached (`config:cache`). Always use `config(...)`.
9. **DO NOT Expose Internal Exceptions:** Never show database errors, file paths, or stack traces to public visitors.
10. **DO NOT Silently Alter Architecture:** Never invent new architectural patterns or add third-party packages without updating the architecture records.
