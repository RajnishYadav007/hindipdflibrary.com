# Workstream K: Deployment Architecture & Pipeline (No-Docker)

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Target Environment:** Shared Cloud Hosting (StackCP / ServerByt)  
**Auditor:** Lead Solutions Architect & Senior DevOps Engineer  

---

## 1. Executive Summary

Deploying to shared hosting requires a process that respects hosting limitations:
1. **No Docker** on shared servers.
2. **No Node/NPM** build capabilities on the server (assets must be built locally or via GitHub Actions CI).
3. **No destructive migrations** against production data.
4. **Zero-downtime maintenance mode** with bypass tokens.

We establish a 3-tier lifecycle:
$$\text{LOCAL} \longrightarrow \text{STAGING (Subdomain)} \longrightarrow \text{PRODUCTION}$$

---

## 2. Environment Progression Workflow

```
[ LOCAL DEVELOPMENT ]
  ├── Feature branch development
  ├── Tests pass (`php artisan test`)
  ├── Asset build (`npm run build`)
  └── Commit to Git repository
          │
          ▼
[ GITHUB / CI REPOSITORY ]
  ├── Automated Linting & Unit Tests
  ├── GitHub Action compiles Vite/Tailwind assets
  └── Packages deployable artifact
          │
          ▼
[ STAGING: staging.hindipdflibrary.com ]
  ├── Deployed in isolated sub-account / directory
  ├── Real MySQL staging database
  ├── Full end-to-end verification (Filament admin, Livewire frontend, SEO tags)
  └── User Sign-Off
          │
          ▼
[ PRODUCTION: hindipdflibrary.com ]
  ├── Scheduled maintenance window
  ├── Database migration execution
  ├── Atomic cutover / cache warmup
  └── Healthcheck verification
```

---

## 3. Shared Hosting Deployment Blueprint (Step-by-Step)

### Phase 1: Local Preparation & Asset Compilation
Because shared hosting servers do not have Node/NPM:
```bash
# 1. Compile production frontend assets
npm run build

# 2. Verify all tests pass
php artisan test

# 3. Ensure dev packages are excluded
composer install --no-dev --optimize-autoloader
```

### Phase 2: Deployment Execution on Server
Whether executed via an SSH deployment script or a GitHub Actions runner:

```bash
# 1. Activate maintenance mode with secret bypass cookie
php artisan down --secret="DEPLOY_BYPASS_TOKEN_xyz" --render="errors::503"

# 2. Pull latest code or extract CI artifact
git pull origin main

# 3. Update Composer dependencies (production only)
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# 4. Run database migrations safely
php artisan migrate --force

# 5. Ensure public storage symlink exists
php artisan storage:link

# 6. Optimize and prime production caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Warm up custom cache keys if necessary
php artisan library:warmup-cache

# 8. Deactivate maintenance mode
php artisan up
```

---

## 4. Zero-Downtime Directory Structure (Symlink Release Pattern)

For hosts with SSH access, a release-based directory structure prevents downtime during file transfers:

```
/home/sites/hindipdflibrary.com/
├── current -> releases/20260913_120000/   <-- Active release symlink
├── shared/
│   ├── .env                               <-- Persistent production environment file
│   └── storage/                           <-- Persistent user media & logs
└── releases/
    ├── 20260910_100000/
    ├── 20260912_150000/
    └── 20260913_120000/                   <-- New release
```

**Cutover Command:**
```bash
ln -sfn /home/sites/hindipdflibrary.com/releases/20260913_120000 /home/sites/hindipdflibrary.com/current
```
Switching the symlink is instantaneous ($< 5\text{ms}$), ensuring visitors never experience partial file reads during deployment.

---

## 5. Rollback Strategy

If a critical error is detected immediately post-deployment:

1. **Symlink Rollback (Instant):**
   ```bash
   ln -sfn /home/sites/hindipdflibrary.com/releases/PREVIOUS_TIMESTAMP /home/sites/hindipdflibrary.com/current
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   ```
2. **Database Rollback:**
   - Database migrations must include fully tested `down()` methods.
   - For major schema changes, a full pre-deployment MySQL dump (`mysqldump -u ... -p ... > pre_deploy.sql`) must be taken before running `php artisan migrate --force`.
   - In catastrophic failure, restore the pre-deployment dump immediately.
