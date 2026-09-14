# Phase 12 Backup, Disaster Recovery & Restore Procedures

## 1. Overview & Objectives
A robust backup and recovery strategy ensures business continuity and protects against server failures, human error, data corruption, and ransomware.

### Targets:
- **Recovery Point Objective (RPO):** Maximum 24 hours of data loss (daily database snapshots).
- **Recovery Time Objective (RTO):** Service restoration within 2 hours of incident declaration.

---

## 2. Backup Scope & Assets
The backup routine covers two distinct data categories:
1. **Relational Database (`hindipdflibrary_db`):**
   - Book catalog, translations, users, reviews, taxonomy, analytics, and redirects.
2. **File Storage Assets:**
   - `storage/app/private_pdfs/`: Digital library PDFs (critical business assets).
   - `storage/app/public/`: Book covers, author avatars, and UI media.
   - `.env` and configuration files (stored encrypted in secure offsite repository).

---

## 3. Automated Backup Routines

### 3.1 MySQL Database Backup Script (`backup_db.sh`)
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/username/backups/db"
DB_NAME="hindipdflibrary_db"
DB_USER="hpdf_user"
DB_PASS="SecurePasswordHere"

mkdir -p "$BACKUP_DIR"

# Generate compressed MySQL dump with single transaction for consistency
mysqldump -u "$DB_USER" -p"$DB_PASS" \
  --single-transaction \
  --quick \
  --routines \
  --triggers \
  "$DB_NAME" | gzip > "$BACKUP_DIR/db_backup_${DATE}.sql.gz"

# Retain last 14 daily backups
find "$BACKUP_DIR" -type f -name "*.sql.gz" -mtime +14 -delete
```

### 3.2 Private Media & PDF Backup Script (`backup_media.sh`)
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/username/backups/files"
SOURCE_DIR="/home/username/public_html/storage/app"

mkdir -p "$BACKUP_DIR"

# Archive private PDFs and public uploads
tar -czf "$BACKUP_DIR/media_backup_${DATE}.tar.gz" \
  -C "$SOURCE_DIR" private_pdfs public

# Retain last 7 weekly archives
find "$BACKUP_DIR" -type f -name "*.tar.gz" -mtime +28 -delete
```

---

## 4. Disaster Recovery & Restoration Procedures

### Step 1: Database Restoration
```bash
# Decompress and import database dump into clean MySQL database
gunzip < /path/to/db_backup_YYYYMMDD_HHMMSS.sql.gz | mysql -u hpdf_user -p hindipdflibrary_db
```

### Step 2: Media Restoration
```bash
# Extract media archives into storage/app directory
tar -xzf /path/to/media_backup_YYYYMMDD_HHMMSS.tar.gz -C /path/to/public_html/storage/app/
```

### Step 3: Application Initialization & Cache Clearing
```bash
cd /path/to/public_html
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan optimize
```

### Step 4: Integrity Verification
- Load homepage: verify featured titles and translations render.
- Test PDF streaming: verify a public domain title streams correctly.
- Test admin access: log in as admin and verify dashboard metrics.
