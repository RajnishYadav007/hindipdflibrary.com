# Phase 1: Environment & Runtime Specification

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Status:** Verified  
**Date:** September 13, 2026  

---

## 1. Local Development Environment
- **Operating System:** Windows 11 / Server x64
- **PHP CLI Version:** 8.5.8 (Scoop x64 ZTS, Zend OPcache enabled)
- **Composer Version:** 2.9.2
- **Node.js:** v22.20.0
- **NPM:** 10.9.3
- **Local MySQL Server:** MariaDB 10.4.32 (XAMPP daemon running on 127.0.0.1:3306)
- **Local Database:** `hindipdflibrary` (Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`)
- **PHP Extensions Loaded:** `ctype, curl, dom, fileinfo, filter, hash, mbstring, openssl, pcre, PDO, pdo_mysql, pdo_sqlite, session, tokenizer, xml, zip, gd, bcmath, intl, Zend OPcache`.

---

## 2. Shared Hosting Target Baseline
- **Web Server:** Apache with StackCDN edge caching proxy
- **Production PHP:** 8.4.25 (verified live on domain)
- **Driver Alignments:**
  - `SESSION_DRIVER=database`
  - `CACHE_STORE=file`
  - `QUEUE_CONNECTION=database`
  - `FILESYSTEM_DISK=public`
  - Zero Redis requirement for V1.
