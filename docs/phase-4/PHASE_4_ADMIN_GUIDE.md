# Phase 4 Admin User Guide

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Portal:** `/admin`  

---

## 1. Accessing the Admin Panel

Navigate to `https://hindipdflibrary.com/admin` in any modern web browser.
- If not logged in, you will be redirected to the secure admin login form (`/admin/login`).
- Only users with verified administrative roles (`Super Admin`, `Admin`, `Editor`, `Moderator`) or `is_admin = true` will be granted entry.
- All other authenticated users receive `403 Forbidden`.

---

## 2. Managing Content

### 2.1. Books Catalog
Navigate to **Content ➔ Books**:
- **Creating a Book**:
  1. **Tab 1: Basic Information**: Enter primary URL slug (kebab-case) and initial status (`Draft`, `Published`, or `Archived`).
  2. **Tab 2: Classification**: Select associated Authors, primary and secondary Categories, Genres, and primary Language.
  3. **Tab 3: Publication Details**: Enter publication year, publisher name, ISBN/ISBN-13, and page count.
  4. **Tab 4: Multilingual Content**: Add title, subtitle, and full description per locale (`hi`, `en`, `sa`) using the repeater.
  5. **Tab 5: Digital File & Legal Source**: Attach PDF document path, source URL, license type (e.g., Public Domain, CC-BY), and copyright status.
- **Bulk Actions**: Select multiple books from the table to batch publish or batch archive records in one click.

### 2.2. Authors Catalog
Navigate to **Content ➔ Authors**:
- Manage author profiles, birth and death years, Wikipedia references, and Hindi/English translations for author names and biographies.

### 2.3. Categories & Hierarchy
Navigate to **Content ➔ Categories**:
- Build nested hierarchical taxonomies (e.g. `Literature` ➔ `Hindi Literature` ➔ `Novels`).
- The parent category selector automatically excludes the record itself to guarantee circular parent-child loops cannot occur.

### 2.4. Languages & Genres
Navigate to **Content ➔ Languages** and **Content ➔ Genres**:
- Register standard language codes (`hi`, `en`, `sa`, etc.) and define genre classifications for book discovery.

---

## 3. Moderation Workflows

Navigate to **Moderation ➔ Reviews**:
- View user-submitted reviews in a streamlined triage table.
- Filter by status (`Pending`, `Approved`, `Rejected`, `Spam`) or star rating.
- Single click actions:
  - **Approve**: Immediately marks the review approved and updates the book's verified review count.
  - **Reject**: Hides the review from the public book view.
  - **Spam**: Tags the entry as malicious and flags the user IP/agent hash for audit.
- Bulk actions allow approving or rejecting dozens of submissions simultaneously.

---

## 4. Media & SEO Management

### 4.1. Media Library
Navigate to **Media & SEO ➔ Media**:
- Inspect uploaded files, disk storage locations, file sizes, and MIME types.
- Visual badges clearly distinguish public website assets (`public` disk) from private digital PDFs (`private_pdfs` disk).

### 4.2. SEO Directives
Navigate to **Media & SEO ➔ SEO Directives**:
- Fine-tune metadata for individual books or collection pages: canonical URLs, robot meta tags (`index, follow`, `noindex, follow`, etc.), OpenGraph social sharing preview images, and Schema.org structured types.

### 4.3. URL Redirects
Navigate to **Media & SEO ➔ URL Redirects**:
- Manage legacy 301/302 URL forwards when URLs are modified or imported from legacy systems.
- Monitor hit counters and last-accessed timestamps.
