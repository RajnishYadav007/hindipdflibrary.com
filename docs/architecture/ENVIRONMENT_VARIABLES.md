# Workstream O: Environment Variable Specification & Governance

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Scope:** Environment Management & Security Standards  
**Auditor:** Lead Security Engineer & DevOps Engineer  

---

## 1. Executive Summary & Security Rules

> [!CAUTION]
> **Strict Secret Governance:**
> 1. Never commit `.env` or production credentials into Git.
> 2. The `.env.example` file acts as the single source of truth for all required configuration keys.
> 3. Any new configuration key added to the application must have a documented placeholder added to `.env.example` in the same commit.
> 4. All production secrets (`APP_KEY`, `DB_PASSWORD`, `GOOGLE_CLIENT_SECRET`, `MAIL_PASSWORD`) must be generated independently on the production server.

---

## 2. Environment Variable Reference

### 2.1. Application & Core Settings
| Variable | Description | Local Value | Production Staging Value | Production Live Value |
| :--- | :--- | :--- | :--- | :--- |
| `APP_NAME` | Name displayed in emails, titles, and SEO | `"Hindi PDF Library"` | `"Hindi PDF Library"` | `"Hindi PDF Library"` |
| `APP_ENV` | Application environment mode | `local` | `staging` | `production` |
| `APP_KEY` | 32-byte AES-256 encryption key | Generated via `artisan` | Generated via `artisan` | Generated independently |
| `APP_DEBUG` | Detailed debug error pages | `true` | `false` | `false` (CRITICAL) |
| `APP_URL` | Base canonical domain | `http://localhost:8000` | `https://staging.hindipdflibrary.com` | `https://hindipdflibrary.com` |
| `APP_TIMEZONE` | Default application timezone | `Asia/Kolkata` | `Asia/Kolkata` | `Asia/Kolkata` |
| `APP_LOCALE` | Default language | `en` | `en` | `en` |
| `APP_FALLBACK_LOCALE`| Fallback language | `en` | `en` | `en` |

### 2.2. Database Configuration
| Variable | Description | Local Value | Production Recommended |
| :--- | :--- | :--- | :--- |
| `DB_CONNECTION` | Database driver | `mysql` (or `sqlite`) | `mysql` |
| `DB_HOST` | Database host | `127.0.0.1` | StackCP internal DB host or `localhost` |
| `DB_PORT` | Port | `3306` | `3306` |
| `DB_DATABASE` | Database name | `hindipdflibrary` | Dedicated production database name |
| `DB_USERNAME` | Database username | `root` | Dedicated non-root MySQL user |
| `DB_PASSWORD` | Database password | Empty/Local | Cryptographically random 32+ char string |
| `DB_CHARSET` | Character set | `utf8mb4` | `utf8mb4` (Required for Hindi) |
| `DB_COLLATION` | Collation | `utf8mb4_unicode_ci` | `utf8mb4_unicode_ci` |

### 2.3. Cache, Queue & Session
| Variable | Description | Local Value | Production Shared Hosting V1 | Future Cloud Value |
| :--- | :--- | :--- | :--- | :--- |
| `CACHE_STORE` | Cache backend | `file` | `file` or `database` | `redis` |
| `QUEUE_CONNECTION`| Queue driver | `sync` or `database`| `database` | `redis` |
| `SESSION_DRIVER` | Session storage | `file` or `database`| `database` | `redis` |
| `FILESYSTEM_DISK` | Storage driver | `public` | `public` | `s3` or `r2` |

### 2.4. Mail Configuration (Transactional)
| Variable | Description | Development | Production |
| :--- | :--- | :--- | :--- |
| `MAIL_MAILER` | Mail driver | `smtp` or `log` | `smtp` (Transactional provider) |
| `MAIL_HOST` | SMTP server | `smtp.mailtrap.io` | `smtp.resend.com` / Brevo / Postmark |
| `MAIL_PORT` | Port | `2525` | `587` or `465` |
| `MAIL_USERNAME` | SMTP login | User token | API username |
| `MAIL_PASSWORD` | SMTP password | Secret | API key |
| `MAIL_ENCRYPTION`| Encryption | `tls` | `tls` |
| `MAIL_FROM_ADDRESS`| Verified sender email | `no-reply@hindipdflibrary.com` | `no-reply@hindipdflibrary.com` |

### 2.5. Third-Party Authentication (Google OAuth)
| Variable | Description | How to Obtain |
| :--- | :--- | :--- |
| `GOOGLE_CLIENT_ID` | OAuth Client ID | Google Cloud Console ➔ Credentials ➔ OAuth 2.0 Client IDs |
| `GOOGLE_CLIENT_SECRET`| OAuth Secret | Google Cloud Console ➔ Credentials ➔ OAuth 2.0 Client IDs |
| `GOOGLE_REDIRECT_URI` | Authorized Redirect URI | `${APP_URL}/auth/google/callback` |

### 2.6. Security & Administration Settings
| Variable | Description | Default Value | Purpose |
| :--- | :--- | :--- | :--- |
| `FILAMENT_PATH` | Admin panel URL path | `portal-control-hpl` | Obscures admin panel from automated bot crawlers |
| `MAX_PDF_UPLOAD_MB` | Maximum PDF file upload size | `50` | Enforces storage defense |
| `MAX_IMAGE_UPLOAD_MB`| Maximum image size | `10` | Enforces media memory defense |
| `BACKUP_DISK` | Destination disk for backups | `local` | Offsite `r2` in production |
| `BACKUP_ARCHIVE_PASSWORD` | Encryption password for backups | Strong random pass | Encrypts database dumps |
