# Workstream N: Domain, Document Root & Directory Isolation

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Platform:** StackCP / ServerByt Shared Linux Hosting  
**Auditor:** Lead Solutions Architect & DevOps Engineer  

---

## 1. The Critical Security Mandate

> [!CAUTION]
> **FATAL MISTAKE TO PREVENT:**
> Dumping an entire Laravel project directly into `public_html/` so that `https://hindipdflibrary.com/.env` is accessible via a web browser is the #1 vulnerability on shared hosting. 
> 
> Doing so exposes database passwords, APP_KEY, mail credentials, and API secrets.
> 
> **Rule:** Only files inside Laravel's `public/` directory may ever be accessible to the public internet.

---

## 2. Recommended Directory Architecture on StackCP

On StackCP/ServerByt platforms, the user's home directory path follows this structure:
`/home/sites/yourdomain.com/`

The ideal layout places the Laravel application completely outside the web root:

```
/home/sites/hindipdflibrary.com/
│
├── laravel-app/                       <-- SECURE APPLICATION DIRECTORY (PRIVATE)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env                           <-- SECURE & BLOCKED FROM WEB
│   ├── artisan
│   └── composer.json
│
└── public_html/                       <-- PUBLIC WEB ROOT (ACCESSIBLE VIA HTTP)
    ├── .htaccess                      <-- Standard Laravel rewrite rules
    ├── favicon.ico
    ├── index.php                      <-- Front controller
    ├── robots.txt
    ├── build/                         <-- Compiled Vite JS & CSS
    └── storage -> /home/.../storage/app/public  <-- Symlink for public uploads
```

---

## 3. Configuration Approaches on StackCP

### Method A: Native Document Root Modification (Recommended)
Many modern StackCP packages allow the Document Root to be configured directly within the panel:
1. Log in to StackCP control panel for `hindipdflibrary.com`.
2. Locate **Domain Settings** ➔ **Web Configuration** ➔ **Document Root**.
3. Change Document Root from `/public_html` to `/laravel-app/public`.
4. Click Save.
*Result:* Cleanest architecture; no symlink workarounds needed.

### Method B: Symlink Cutover (If Document Root is Locked to `public_html`)
If StackCP does not allow changing the web root:
1. Upload the Laravel project to `/home/sites/hindipdflibrary.com/laravel-app`.
2. Back up and remove the old WordPress `public_html/` folder.
3. Create a symbolic link pointing `public_html` to `laravel-app/public`:
   ```bash
   ln -s /home/sites/hindipdflibrary.com/laravel-app/public /home/sites/hindipdflibrary.com/public_html
   ```

### Method C: Front-Controller Path Adjustment (Fallback)
If symlinks are disabled by the hosting kernel:
1. Place the entire Laravel application in `/home/sites/hindipdflibrary.com/laravel-app`.
2. Copy the contents of `laravel-app/public/` into `public_html/`.
3. Update `/home/sites/hindipdflibrary.com/public_html/index.php` to point to the correct vendor and bootstrap paths:
   ```php
   // Update paths in public_html/index.php
   require __DIR__.'/../laravel-app/vendor/autoload.php';
   $app = require_once __DIR__.'/../laravel-app/bootstrap/app.php';
   ```

---

## 4. Staging Subdomain Architecture

Before modifying the live `hindipdflibrary.com` web root (which currently runs WordPress):
1. In StackCP, navigate to **Subdomains**.
2. Create: `staging.hindipdflibrary.com`.
3. Set the Document Root for the subdomain to:
   `/home/sites/hindipdflibrary.com/staging-app/public`.
4. All Phase 1 and Phase 2 testing will occur on `staging.hindipdflibrary.com` without touching the live WordPress site.
5. Only during final cutover will production `hindipdflibrary.com` be redirected to the new Laravel codebase.
