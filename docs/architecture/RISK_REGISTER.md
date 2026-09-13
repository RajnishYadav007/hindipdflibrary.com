# Workstream S: Risk Register & Mitigation Strategy

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Scope:** Comprehensive Architecture, Operational & Business Risks  
**Auditor:** Lead Solutions Architect & Technical Project Manager  

---

## 1. Risk Evaluation Matrix

| ID | Risk Description | Prob. | Impact | Severity | Preventive Mitigation Strategy | Fallback / Recovery Plan |
| :- | :--- | :--- | :--- | :--- | :--- | :--- |
| **R-01** | **Shared Hosting Process Termination** (Background queue daemon killed) | High | High | **HIGH** | Use `--stop-when-empty` ephemeral worker triggered via Cron every 2 mins. | If cron worker hangs, execute queue via sync or direct batch command. |
| **R-02** | **PHP Version Mismatch on Production** | Low | High | **LOW** | Verified: Production server is already running PHP 8.4.25 (meets Laravel 13 ^8.3). | Pin PHP version in StackCP PHP selector if host changes defaults. |
| **R-03** | **Memory Exhaustion During Media/PDF Uploads** | Med | Med | **MED** | Offload image conversions to background queue; size thumbnails conservatively with GD. | Limit max simultaneous uploads; increase web memory limit in StackCP panel. |
| **R-04** | **PDF Storage Exhaustion on Shared Disk** | Med | High | **HIGH** | Set 50MB max PDF upload rule; monitor disk usage via automated notification. | Mount Cloudflare R2 / AWS S3 bucket as `private_pdfs` disk when storage hits 80%. |
| **R-05** | **Redis Unavailability** | High | Low | **LOW** | Strict Cache & Queue contracts; use `file` or `database` drivers in V1. | Application code has zero Redis dependencies; runs seamlessly on DB/file. |
| **R-06** | **Search Performance Degradation on Large Catalog** | Med | Med | **MED** | MySQL composite FULLTEXT indexes + B-tree taxonomy indexes; cache top queries. | Upgrade to Meilisearch in Phase 2+ when catalog exceeds 25,000 titles. |
| **R-07** | **Traffic Spikes Overwhelming Shared MySQL** | Med | High | **HIGH** | StackCDN edge caching on public GET routes; Laravel Cache on heavy model queries. | Enable Cloudflare full-page caching or scale up to managed VPS. |
| **R-08** | **SEO Traffic Collapse Due to Broken WordPress URLs** | Med | Crit | **CRITICAL** | Comprehensive legacy URL extraction; automated 301 redirection table (`legacy_redirects`). | Verify redirects against Screaming Frog crawl before DNS cutover. |
| **R-09** | **Duplicate Content Penalties Across Languages** | Med | Med | **MED** | Strict `<link rel="canonical">` and bidirectional `<link rel="alternate" hreflang="...">` tags. | Run automated SEO audit via Google Search Console and Lighthouse. |
| **R-10** | **Malicious File Upload (Web Shell / Executable)** | Med | Crit | **CRITICAL** | Strict PHP `finfo` magic byte validation; discard client filenames; store outside web root. | Delete corrupted file immediately; quarantine upload directory; ban IP. |
| **R-11** | **Fake Reviews / Spam Ratings Abuse** | High | Med | **MED** | Require email verification or Google OAuth to review; rate limit submissions; Filament moderation. | Batch delete spam via Filament table actions; auto-flag suspicious IPs. |
| **R-12** | **Analytics Ingestion Table Unbounded Growth** | High | Med | **MED** | Buffer table (`page_views_buffer`) truncated hourly after aggregating into daily summary tables. | Run emergency prune command (`php artisan analytics:prune-buffer`). |
| **R-13** | **Shared Hosting Backup Failure / Corruption** | Low | Crit | **CRITICAL** | Encrypted automated daily offsite backup to Cloudflare R2 via Spatie Backup. | Maintain secondary manual weekly database export in secure storage. |
| **R-14** | **Accidental Production Database Clobbering** | Low | Crit | **CRITICAL** | Separate local, staging, and production database credentials. `migrate:fresh` blocked. | Immediate point-in-time restoration from daily encrypted offsite backup. |
| **R-15** | **Edge Cache Inadvertently Caching Auth Sessions** | Med | Crit | **CRITICAL** | Enforce `Cache-Control: private, no-store` on all authenticated and Filament admin responses. | Purge StackCDN / Cloudflare cache instantly via API or control panel. |

---

## 2. Risk Mitigation Protocols

### 2.1. Critical SEO Safeguard (R-08)
Before cutover from WordPress to Laravel:
1. Crawl current WordPress site to export 100% of live URLs into `wp_urls.csv`.
2. Seed all URLs into `legacy_redirects` table mapped to corresponding Laravel book slugs.
3. Test every legacy URL in staging to confirm HTTP 301 redirect with correct target location.

### 2.2. Zero-Data-Loss Backup Protocol (R-13 & R-14)
1. Run automated daily mysqldump at 02:00 UTC.
2. Encrypt dump with AES-256 password before transmission.
3. Transmit offsite to Cloudflare R2 (independent cloud provider).
4. Perform quarterly test restore drills on local environment.
