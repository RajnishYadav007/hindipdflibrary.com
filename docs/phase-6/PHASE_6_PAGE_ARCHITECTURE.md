# Phase 6 — Page Architecture & Routing Specification

## 1. Information Architecture Overview
The public information architecture is structured hierarchically around cultural discovery, academic research, and reading convenience:

```
                  ┌───────────────────────┐
                  │    Homepage  (/)      │
                  └───────────┬───────────┘
                              │
         ┌────────────────────┼────────────────────┐
         ▼                    ▼                    ▼
┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐
│ Book Page        │ │ Author Page      │ │ Category Page    │
│ (/books/{slug})  │ │ (/authors/{slug})│ │(/categories/{...}│
└────────┬─────────┘ └────────┬─────────┘ └────────┬─────────┘
         │                    │                    │
         └────────────────────┼────────────────────┘
                              ▼
                  ┌───────────────────────┐
                  │ Search & Catalog Page │
                  │      (/search)        │
                  └───────────────────────┘
```

## 2. Controller Design Pattern
In compliance with `AGENTS.md` and standard Laravel practices:
1. **Thin Controllers**: Controllers accept incoming requests, validate query parameters, delegate query scoping to Eloquent models, and return Blade views.
2. **Atomic Action Separation**: Future mutation flows (such as review submission, favorite bookmarking) will reside strictly in `app/Actions/` during Phase 7.
3. **No Direct Redis Calls**: Cache and session state remain standard Laravel abstractions ready for shared hosting deployment.

## 3. Route Aliases & SEO Redirects
Singular URL aliases are registered to preserve legacy backlinks and avoid 404 dead ends:
```php
Route::get('/book/{slug}', fn (string $slug) => redirect()->route('books.show', ['slug' => $slug], 301));
Route::get('/author/{slug}', fn (string $slug) => redirect()->route('authors.show', ['slug' => $slug], 301));
Route::get('/category/{slug}', fn (string $slug) => redirect()->route('categories.show', ['slug' => $slug], 301));
```
All redirects emit HTTP status `301 Moved Permanently` to consolidate search engine indexing equity onto canonical plural routes.
