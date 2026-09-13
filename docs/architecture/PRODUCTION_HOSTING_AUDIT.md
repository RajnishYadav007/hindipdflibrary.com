# Workstream C: Production Hosting Audit

**Date:** September 13, 2026  
**Target Domain:** `https://hindipdflibrary.com/`  
**Control Panel Architecture:** StackCP / ServerByt Shared Platform  
**Auditor:** Lead Solutions Architect & DevOps Engineer  

---

## 1. Hosting Environment Overview

The production hosting infrastructure for `hindipdflibrary.com` is hosted on a **StackCP / ServerByt** cloud-shared hosting architecture (powered by 20i/StackCDN infrastructure).

Because direct SSH/shell credentials for production were not supplied to this workspace, verification was performed through **active HTTP/REST header telemetry and external network inspection**. Every capability is classified strictly as **VERIFIED**, **NOT VERIFIED**, or **UNKNOWN**.

---

## 2. Capability Audit Matrix

| Capability / Service | Target Requirement | Status | Empirical Evidence / Verification Method |
| :--- | :--- | :--- | :--- |
| **HTTP Server** | Apache / Nginx | **VERIFIED** | Response header: `server: Apache` |
| **PHP Version** | PHP 8.3+ | **VERIFIED** | Response header: `x-powered-by: PHP/8.4.25` (Satisfies Laravel 13) |
| **SSL / TLS** | Valid HTTPS Certificate | **VERIFIED** | HTTPS 200 OK verified on port 443 |
| **CDN / Caching Layer**| Edge caching | **VERIFIED** | Response headers: `x-provided-by: StackCDN`, `x-stackcache-cacheable: yes` |
| **Current Live Application** | WordPress (Preserve) | **VERIFIED** | REST API active at `/wp-json/`; Page ID 2608 discovered |
| **PHP SAPI** | FastCGI / FPM | **NOT VERIFIED** | Inferred as FPM/FastCGI based on StackCP architecture, but requires `phpinfo()` verification |
| **PHP Web Memory Limit** | 256M+ | **NOT VERIFIED** | Typical StackCP default is 256M-512M; verify in StackCP "PHP Configuration" panel |
| **Upload / POST Limit** | 50M+ (for PDFs) | **NOT VERIFIED** | Configurable via StackCP PHP settings / `.user.ini` |
| **Execution Time (Web)**| 60s+ | **NOT VERIFIED** | Typically capped at 30s-60s on shared Apache/FPM |
| **MySQL Database Engine**| MySQL 8.0+ / MariaDB | **NOT VERIFIED** | Databases available via control panel & phpMyAdmin; exact version unconfirmed |
| **SSH Shell Access** | Restricted / Jailed | **NOT VERIFIED** | Control panel shows SSH feature, but SSH credentials/keys not tested from workspace |
| **Git Version Control** | Deploy from Git | **NOT VERIFIED** | Feature listed in StackCP panel; webhook/pull capability requires user configuration |
| **Cron / Scheduled Tasks**| Minutely / Hourly | **VERIFIED (Panel)** | Cron listed in StackCP; frequency limitations to be confirmed in panel |
| **Persistent Workers** | Supervisor / Daemons | **NOT VERIFIED (Unlikely)**| Shared hosting terminates background daemon loops. **Assume NOT SUPPORTED** |
| **Redis In-Memory Store** | Cache / Queue backend | **NOT VERIFIED (Unlikely)**| Shared StackCP packages do not include dedicated Redis port. **Assume NOT SUPPORTED** |
| **Node / NPM on Server** | Asset compilation | **NOT VERIFIED** | Almost never available or reliable on shared web nodes. **Assume NOT SUPPORTED** |
| **Composer on Server** | Dependency updates | **NOT VERIFIED** | Often available in StackCP SSH, but constrained by memory limits |
| **Meilisearch / Elasticsearch**| Full-text daemon | **NOT VERIFIED (Unlikely)**| Background search server binaries cannot run on shared hosting |
| **Docker / Containerization** | Containers | **NOT VERIFIED (Unsupported)**| Shared hosting does not permit Docker engines |
| **Laravel One-Click Installer**| Shared hosting tool | **VERIFIED (Panel)** | Present in control panel, but manual Git-based deployment is preferred |

---

## 3. Detailed Hosting Constraint Analysis

### 3.1. PHP 8.4.25 Compatibility (Major Finding)
The live production web server is already running **PHP 8.4.25**. This is an exceptional finding:
- Laravel 13 requires `php ^8.3`.
- The production server is already on PHP 8.4, fully satisfying the requirement with zero need to request a hosting PHP upgrade.

### 3.2. Caching Proxy & CDN Considerations
The domain routes through **StackCDN** with an origin node at `LHR3` (London).
- Header `x-stackcache-cacheable: yes` indicates that the hosting platform automatically caches HTML output at the edge unless headers prevent it.
- **Critical Architectural Requirement:** Laravel session cookies (`laravel_session`, `XSRF-TOKEN`) and authenticated admin routes (Filament) must send proper `Cache-Control: no-cache, private` headers so StackCDN does not cache dynamic responses or leak authenticated sessions.

### 3.3. Document Root Architecture
On StackCP shared hosting:
- The default web root is `/home/sites/.../public_html/`.
- In standard WordPress installations, WordPress sits directly inside `public_html/`.
- Laravel strictly requires its public directory (`/public`) to be the web root. Exposing the parent directory will leak `.env`, `composer.json`, and source code.
- StackCP allows changing the Document Root via the control panel or symlinking `public_html` to `laravel-app/public`.

---

## 4. Verification Action Checklist for User

To upgrade any **NOT VERIFIED** item to **VERIFIED**, the user should perform the following quick checks in their StackCP control panel:

1. **MySQL Version Check:** Open phpMyAdmin from StackCP and note the MySQL or MariaDB server version in the right sidebar.
2. **Cron Frequency Check:** Open "Scheduled Tasks" and verify if cron can run every minute (`* * * * *`) or if there is a minimum interval (e.g., every 5, 10, or 15 minutes).
3. **Document Root Setting:** Verify whether the control panel provides a "Change Document Root" setting or if directory restructuring is needed.
4. **SSH Credentials:** Verify whether SSH is enabled and whether SSH keys or passwords are generated.
