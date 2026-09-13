# Workstream H: Cache Strategy & Performance Blueprint

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Component:** Application Caching, Edge Caching, and Invalidation  
**Host Context:** Shared Hosting (No Redis Assumed)  
**Auditor:** Lead Solutions Architect & Senior Laravel Engineer  

---

## 1. Core Principles

1. **Strict Abstraction:** Never call `Redis::...` or `Predis::...` directly in controllers, models, or services. All caching must strictly utilize Laravel's `Illuminate\Support\Facades\Cache` or dependency-injected `Illuminate\Contracts\Cache\Repository`.
2. **Zero Infrastructure Coupling:** The caching backend must be interchangeable via the `.env` `CACHE_STORE` key (`file`, `database`, or `redis`) with zero application code changes.
3. **Multi-Tier Caching:** Caching is divided into:
   - Tier 1: Edge CDN Caching (StackCDN / Cloudflare) for public static assets and cacheable HTML.
   - Tier 2: Application Query & Object Caching (Laravel Cache facade).
   - Tier 3: Framework Configuration & Route Caching (`php artisan config:cache`, `route:cache`, `view:cache`).

---

## 2. Cache Driver Evaluation for Shared Hosting

| Driver | Shared Hosting Feasibility | Performance | Concurrency / Locking | Recommendation |
| :--- | :--- | :--- | :--- | :--- |
| **`file`** | **100% Feasible** | Very fast for low-to-medium I/O | File lock contention under high traffic | **Recommended Default (Local & Low Traffic)** |
| **`database`** | **100% Feasible** | Fast (uses MySQL indexed table) | Handles concurrent transactions cleanly | **Recommended Alternative (Multi-Worker / High Traffic)** |
| **`redis`** | **UNVERIFIED (Requires Dedicated Server)** | Sub-millisecond in-memory | Atomic operations & tags supported | **Future Cloud Upgrade Only** |
| **`memcached`**| **UNVERIFIED (Not available on StackCP)** | In-memory | Simple key-value | **Not Recommended** |
| **`array`** | **100% Feasible** | In-memory per request | Ephemeral (dies with request) | **Used in Automated Unit Testing** |

### V1 Decision:
Default to `CACHE_STORE=file` for initial deployment. If file I/O latency or file permission issues occur on shared storage, switch immediately to `CACHE_STORE=database` by creating the `cache` table via standard Laravel migration.

---

## 3. Application Caching Patterns

### 3.1. What to Cache
1. **Taxonomies & Categories:**
   Categories, genres, and language lists change infrequently.
   ```php
   $categories = Cache::remember('taxonomies:categories:all', now()->addHours(24), function () {
       return Category::withCount('books')->orderBy('name')->get();
   });
   ```
2. **Popular / Featured Books (Homepage):**
   Heavy aggregation queries (ratings, review counts, download counts) cached for 30–60 minutes.
   ```php
   $featuredBooks = Cache::remember('books:featured:homepage', now()->addMinutes(30), function () {
       return Book::with(['author', 'category'])
           ->where('is_featured', true)
           ->limit(12)
           ->get();
   });
   ```
3. **Book Detail Metadata:**
   Individual book detail pages cached for 1–6 hours:
   ```php
   $book = Cache::remember("book:slug:{$slug}", now()->addHours(6), function () use ($slug) {
       return Book::with(['author', 'genres', 'category', 'media'])->where('slug', $slug)->firstOrFail();
   });
   ```
4. **Site Settings & Navigation:**
   Cached for 24 hours.

### 3.2. What NOT to Cache
- User authentication state, user cart, or user session data.
- Search result pages with dynamic search query strings (to prevent cache explosion).
- First-party analytics logging buffers.
- Admin dashboard metrics (Filament live views).

---

## 4. Cache Invalidation Strategy (Tag-Free Architecture)

> [!IMPORTANT]
> The `file` and `database` cache drivers in Laravel do **not** support Cache Tags (`Cache::tags(...)`). Tagging is only supported by Redis and Memcached.

To ensure our application code remains 100% compatible with `file` and `database` cache stores, we implement **Key-Based Event Invalidation** instead of relying on `Cache::tags()`:

```php
namespace App\Observers;

use App\Models\Book;
use Illuminate\Support\Facades\Cache;

class BookObserver
{
    public function saved(Book $book): void
    {
        // Invalidate specific book cache
        Cache::forget("book:slug:{$book->slug}");
        Cache::forget("book:id:{$book->id}");
        
        // Invalidate global featured/catalog lists
        Cache::forget('books:featured:homepage');
    }

    public function deleted(Book $book): void
    {
        $this->saved($book);
    }
}
```

---

## 5. Edge CDN (StackCDN) Interplay & Header Protection

Production headers showed `x-provided-by: StackCDN` and `x-stackcache-cacheable: yes`.
To prevent edge proxies from caching sensitive responses:

1. **Authenticated / Admin Routes:**
   Filament and user account routes must automatically send anti-caching headers:
   ```
   Cache-Control: no-cache, private, no-store, must-revalidate
   Pragma: no-cache
   ```
2. **Public Static Pages (Homepage, Category Lists):**
   Can safely permit edge caching for non-authenticated guests:
   ```
   Cache-Control: public, max-age=1800, s-maxage=3600
   ```
   StackCDN will serve these directly from its edge nodes (London origin LHR3), dramatically lowering load on shared MySQL.
