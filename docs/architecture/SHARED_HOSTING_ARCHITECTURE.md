# Workstream F: Shared Hosting Architecture Blueprint

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Architecture Phase:** Phase 0 Baseline  
**Strategy:** Shared-Hosting First with Zero-Lock-In Upgrade Path  
**Auditor:** Lead Solutions Architect & Senior DevOps Engineer  

---

## 1. Architectural Philosophy

The V1 architecture is designed strictly around the realities of **shared hosting (StackCP / ServerByt)**:
- Low memory consumption (< 256MB per web request).
- No mandatory background daemons (No persistent queue workers, no Supervisor, no Docker, no Meilisearch).
- Pure HTTP request/response cycle handled efficiently by Apache + PHP-FPM.
- Native MySQL 8.0+ utilized for relational data, full-text search, queue jobs, and sessions.
- Clean Laravel service abstractions (`Cache`, `Queue`, `Storage`, `Mail`) ensuring that migrating to a dedicated VPS or Cloud cluster in the future requires **only `.env` changes with zero code rewrites**.

---

## 2. Component Architecture Matrix: V1 vs Future Cloud

| # | Component | V1 Shared Hosting Architecture | Future VPS / Cloud Upgrade Path | Migration Trigger / Threshold |
| :- | :--- | :--- | :--- | :--- |
| **1** | **Application Server** | Shared Apache with FastCGI/PHP-FPM, `.htaccess` routing | Nginx + PHP-FPM or FrankenPHP / Laravel Octane on dedicated Linux VPS | > 50,000 daily active users or high concurrent traffic |
| **2** | **PHP Runtime** | PHP 8.4.25 (verified live on server), Zend OPcache enabled | PHP 8.4+ CLI / FPM with custom process manager tuning | Performance scaling requirement |
| **3** | **Database (MySQL)** | Shared MySQL 8 / MariaDB on internal network cluster | Dedicated managed MySQL instance (AWS RDS / DigitalOcean Managed DB) | > 50GB database size or complex query load |
| **4** | **Cache System** | Laravel `file` cache driver (local disk) or `database` cache table | Redis 7+ in-memory cluster (`phpredis` extension) | Cache contention / I/O latency on shared disk |
| **5** | **Queue System** | Laravel `database` driver triggered via Cron batch execution | Redis Queue + persistent Supervisor daemon workers (`queue:work`) | > 1,000 jobs/minute or synchronous task delays |
| **6** | **Job Scheduler** | StackCP Scheduled Task running `php artisan schedule:run` every 1-5 mins | Linux `systemd` cron running every minute unconditionally | Need for sub-minute task scheduling |
| **7** | **Storage & Media** | Local private filesystem (`storage/app/public`) symlinked to web root | S3-compatible Object Storage (Cloudflare R2 / AWS S3) + CDN | > 20GB media library or multi-server scaling |
| **8** | **Search Engine** | MySQL `FULLTEXT` natural language boolean search on books/authors | Meilisearch or Typesense containerized instance | Multilingual fuzzy matching / typo tolerance needed |
| **9** | **Transactional Email**| External SMTP via transactional API (Resend, Brevo, or Postmark) | External transactional API via dedicated webhook queue | Shared hosting IP reputation degradation |
| **10**| **First-Party Analytics**| Lightweight database-buffered logging table with batch aggregation | Dedicated ClickHouse or Redis time-series database | > 100,000 pageviews/day creating MySQL write load |
| **11**| **Application Logging**| Rotating daily log files (`storage/logs/laravel-YYYY-MM-DD.log`) | Centralized log ingestion (Papertrail / Better Stack / Bugsnag) | Audit compliance or multi-server log aggregation |
| **12**| **Backups** | Scheduled Artisan backup dumping MySQL + zip storage to secondary FTP/R2 | Automated point-in-time cloud snapshots + encrypted offsite storage | Disaster recovery SLA requirement |
| **13**| **Deployment Pipeline**| Git push -> CI build assets -> rsync/SSH deployment or zero-downtime script | GitHub Actions CI/CD with automated Docker / Deployer workflow | Team expansion / zero-downtime requirement |

---

## 3. Subsystem Breakdown for Shared Hosting (V1)

### 3.1. Web Server & Request Lifecycle
- Apache handles initial HTTPS termination and forwards requests to PHP 8.4 FPM via `mod_rewrite` using standard Laravel `.htaccess`.
- StackCDN acts as the outer edge caching layer.
- **Edge Cache Invalidation Rule:** Dynamic pages with authenticated sessions (`laravel_session`) must send `Cache-Control: private, no-store, max-age=0` to prevent StackCDN from caching authenticated user data.

### 3.2. Queue & Background Processing (No Daemon Solution)
- Without Supervisor, standard persistent workers (`artisan queue:work`) are terminated by shared hosting process monitors.
- **V1 Solution:**
  1. Queued jobs (emails, sitemap generation, media conversions) are written to the MySQL `jobs` table (`QUEUE_CONNECTION=database`).
  2. A scheduled cron task runs `php artisan queue:work --stop-when-empty --max-time=50 --memory=128` every 2 minutes.
  3. This executes all pending jobs safely within execution limits and terminates cleanly.

### 3.3. Cache Abstraction
- The application will strictly utilize the `Cache` facade (`Cache::remember()`, `Cache::tags()` where supported, `Cache::get()`).
- In V1, `CACHE_STORE=file` or `CACHE_STORE=database` will be used.
- Zero code references will call `Redis::...` directly. When Redis is provisioned on future infrastructure, changing `CACHE_STORE=redis` in `.env` completes the upgrade instantly.

### 3.4. First-Party Analytics Without Performance Degradation
- To prevent heavy database writes from slowing down page responses:
  1. Requests capture essential telemetry (anonymized IP hash, book slug, referrer, user-agent device type, timestamp).
  2. Telemetry is written to a fast, non-indexed ingestion table (`page_views_buffer`).
  3. A scheduled hourly task aggregates the raw counts into summary reporting tables (`book_analytics_daily`, `traffic_analytics_daily`) and truncates the buffer.
  4. Public page load time remains sub-100ms.

---

## 4. Upgrade Path to Dedicated Infrastructure

When traffic or storage demands outgrow shared hosting:
1. **Zero Architectural Refactoring:** Because all services adhere to Laravel contracts (`Illuminate\Contracts\Cache\Repository`, `Illuminate\Contracts\Queue\Queue`, `Illuminate\Contracts\Filesystem\Filesystem`), no business logic is altered.
2. **Environment Variable Switch:** Moving to Redis and Object Storage is achieved entirely via `.env`:
   ```dotenv
   QUEUE_CONNECTION=redis
   CACHE_STORE=redis
   FILESYSTEM_DISK=s3
   ```
3. **Containerization:** A `Dockerfile` and `docker-compose.yml` can be introduced in Phase 2+ to deploy to Hetzner, AWS, or DigitalOcean with zero application refactoring.
