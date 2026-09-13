# ADR-009: Hybrid Storage Architecture (Public Media vs Protected Digital Books)

**Date:** September 13, 2026  
**Status:** Accepted  
**Deciders:** Lead Solutions Architect, Security Engineer  

---

## Context
A digital library platform handles two distinct classes of media:
1. **Public Marketing Assets:** Book covers, author photos, site banners (must be fast, CDN-cacheable, web-accessible).
2. **Digital Books & Documents (PDF/EPUB):** Legal documents, licensed publications, and copyright-protected digital books (must NOT be indexed directly by crawlers or accessible via static URLs without access verification and metered rate limiting).

## Decision
Establish a **two-tier storage architecture**:
1. **Public Disk (`public`):** Stores optimized WebP/JPEG thumbnails in `storage/app/public/covers/` symlinked to `public/storage/`. Served directly by Apache and cached by StackCDN.
2. **Protected Private Disk (`private_pdfs`):** Stores PDF documents in `storage/app/private_pdfs/` outside the web root. Served strictly through an authenticated, metered streaming controller (`BookDownloadController`).

## Reason
1. **Security & Anti-Scraping:** Prevents competitors and unauthorized scrapers from crawling and mass-downloading the entire PDF collection via simple directory traversal or predictable URL brute-forcing.
2. **Legal Compliance:** Allows verifying copyright/license rules and recording first-party download telemetry before streaming bytes.
3. **Performance Optimization:** Public images bypass PHP entirely, taking advantage of Apache high-speed static file serving and StackCDN edge caching.

## Alternatives Considered
- **All Files Publicly Accessible in `public/uploads/`:** Rejected. Severe legal liability, hotlinking abuse, zero download telemetry, and mass scraping risk.
- **Serving All Images Through PHP Controllers:** Rejected. Crushes server performance by tying up PHP worker processes to serve static cover images.

## Consequences
- Requires running `php artisan storage:link` during deployment.
- PDFs consume server bandwidth via PHP stream responses; mitigated by strict download rate limiting (10 downloads/minute per IP).

## Future Migration Path
When storage capacity exceeds shared hosting limits, switch `private_pdfs` disk driver to Cloudflare R2 / AWS S3 and serve downloads via short-lived, pre-signed temporary URLs.
