# Workstream M: Backup & Disaster Recovery Blueprint

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Scope:** Data Protection, Disaster Recovery, RTO/RPO Standards  
**Auditor:** Lead DevOps & Database Architect  

---

## 1. Executive Summary & Recovery Objectives

A digital library platform stores non-reproducible assets: user accounts, reviews, book ratings, reading metadata, and curated PDF files.

### Disaster Recovery Targets:
- **RPO (Recovery Point Objective):** Maximum 24 hours of data loss in catastrophic disaster (hourly database snapshots reduce this to 1 hour).
- **RTO (Recovery Time Objective):** Full service restoration within 60 minutes.

---

## 2. Backup Targets & Classification

| Target | Content Type | Change Frequency | Backup Frequency | Retention Policy | Storage Destination |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **MySQL Database** | Relational data, users, books, reviews, analytics | High (continuous) | Daily at 02:00 UTC (Hourly optional) | 7 daily, 4 weekly, 6 monthly | Offsite Cloudflare R2 / S3 Bucket |
| **Cover & Author Media**| WebP/JPEG images in `storage/app/public` | Medium (new books added) | Weekly sync | 30 days incremental | Offsite R2 / S3 Bucket |
| **PDF Documents** | Protected digital book files in `private_pdfs` | Low/Medium | Daily sync upon change | Versioned mirror | Offsite R2 / S3 Bucket |
| **Configuration** | `.env.production` (encrypted), SSL certs | Low | On change | Indefinite (Encrypted vault) | Secure Password/Secrets Vault |
| **Source Code** | Git codebase & commit history | On release | Continuous via Git | Permanent | GitHub / GitLab Remote |

---

## 3. Implementation Strategy (Shared Hosting V1)

### 3.1. Database Dump Automation
Because shared hosting does not permit running external daemon agents, backups are handled via a scheduled Artisan command using PHP PDO/mysqldump:

```bash
# Artisan command triggered via Scheduled Task at 02:00 UTC
php artisan backup:run --only-db
```

Using `spatie/laravel-backup` (or custom lightweight backup command):
1. Dumps MySQL database using `mysqldump`.
2. Compresses the `.sql` dump into a `.zip` archive with AES-256 encryption.
3. Automatically streams the archive to a remote, offsite storage disk (`s3`/`r2`).
4. Prunes backups exceeding the retention policy.

### 3.2. Media & PDF Synchronization
PDFs and cover images are synced to offsite storage using a daily incremental sync job:
```bash
php artisan backup:run --only-files
```

---

## 4. Step-by-Step Restoration Procedure (Disaster Recovery)

In the event of total server loss, database corruption, or ransomware compromise:

### Step 1: Infrastructure Provisioning
1. Provision a fresh web root with PHP 8.4+ and MySQL 8.0+.
2. Clone the Git repository:
   ```bash
   git clone git@github.com:your-org/hindipdflibrary.com.git /home/sites/hindipdflibrary.com/app
   cd /home/sites/hindipdflibrary.com/app
   ```

### Step 2: Environment & Secret Restoration
1. Restore `.env` from secure secrets vault.
2. Install production dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

### Step 3: Database Restoration
1. Download latest encrypted MySQL backup from offsite R2 bucket.
2. Decrypt and import into the fresh MySQL database:
   ```bash
   unzip -P "YOUR_BACKUP_PASSWORD" backup-2026-09-13.zip
   mysql -u DB_USER -p DB_NAME < db-dump.sql
   ```

### Step 4: Storage & Media Restoration
1. Sync `storage/app/public` and `storage/app/private_pdfs` from the offsite R2 bucket.
2. Re-create public symlink:
   ```bash
   php artisan storage:link
   ```

### Step 5: Optimization & Verification
1. Run cache and optimization commands:
   ```bash
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
2. Verify HTTP 200 responses on homepage, book detail, and admin login.
3. Point DNS / CDN to the new server IP.
