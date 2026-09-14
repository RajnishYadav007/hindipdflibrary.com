# Phase 12 Production Readiness Checklist

## 1. Environment & Server Configuration (`.env`)
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL=https://hindipdflibrary.com`
- [ ] `APP_KEY` securely generated and unique.
- [ ] `DB_CONNECTION=mysql` with production credentials, non-root user.
- [ ] `DB_CHARSET=utf8mb4` and `DB_COLLATION=utf8mb4_unicode_ci` (required for Hindi script).
- [ ] `CACHE_STORE=file` or `database`.
- [ ] `SESSION_DRIVER=database` or `file`, `SESSION_SECURE_COOKIE=true`.
- [ ] `QUEUE_CONNECTION=database`.

---

## 2. File Permissions & Directory Security
- [ ] Storage symlink established: `php artisan storage:link` (points `public/storage` -> `storage/app/public`).
- [ ] Private PDFs directory created: `storage/app/private_pdfs/` (must NOT be symlinked to `public/`).
- [ ] File permissions:
  - `storage/` and `bootstrap/cache/` writable by web server user (`chmod -R 775`).
  - `.env` file readable only by application owner (`chmod 600` or `chmod 640`).
  - Web root pointed strictly to `/public/` directory in vhost configuration.

---

## 3. Laravel Optimization Commands
Run the following build and cache commands during production deployment:
```bash
# 1. Build frontend assets
npm run build

# 2. Cache configuration, routes, views, and events
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 3. Optimize class loading
composer install --optimize-autoloader --no-dev
```

---

## 4. Scheduled Background Tasks (Cron)
On shared hosting (cPanel / StackCP), configure a single cron entry firing every minute:
```cron
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```
The Laravel scheduler handles:
- Daily analytics aggregation (`analytics:aggregate`).
- 90-day analytics pruning (`analytics:prune`).
- Queue worker execution (`queue:work --stop-when-empty`).
- Sitemap re-generation.

---

## 5. Security & Verification Testing
- [ ] Automated tests pass: `php artisan test`.
- [ ] Security headers active: `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`.
- [ ] Rate limits functional: 429 response on exceeded downloads/searches.
- [ ] SSL / HTTPS active with automated renewal (Let's Encrypt / Cloudflare).
- [ ] Backups scheduled (database and private PDFs).
