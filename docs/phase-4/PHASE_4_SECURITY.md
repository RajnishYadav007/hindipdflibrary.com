# Phase 4 Security Architecture & Controls

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Phase:** Phase 4 — Admin Panel / CMS  
**Security Lead:** Senior Security Engineer & Laravel Architect  

---

## 1. Authentication & Authorization Boundaries

### 1.1. Multi-Layer Panel Guard
- **Guest Interception**: Unauthenticated requests to `/admin/*` are automatically intercepted and redirected to `/admin/login` with HTTPS cookie security.
- **Contract-Based Access**: The `User` model implements `FilamentUser` requiring `canAccessPanel(Panel $panel): bool`. This verifies either `$user->is_admin === true` or membership in authorized roles (`Super Admin`, `Admin`, `Editor`, `Moderator`).
- **Policy Enforcement**: Frontend UI visibility is supplemented by server-side Policy checks on all mutations (`BookPolicy`, `UserPolicy`, `ReviewPolicy`, etc.).

---

## 2. Data Protection & Password Privacy

### 2.1. Password Exposure Prevention
- In `UserForm.php`, the password input field:
  - Is required on user creation.
  - Is optional and **completely concealed/blank** on user edit.
  - Does not reflect hashed passwords into HTML form attributes or Livewire state.
  - Only triggers a hash update if an administrator explicitly enters a new password string.
- Tested and verified by automated test `test_password_hash_is_not_exposed_in_edit_form`.

---

## 3. Digital Asset & PDF Storage Security

### 3.1. Private PDF Disk Isolation
- Digital book PDFs are stored on the `private_pdfs` disk mapped to `storage/app/private_pdfs/`.
- This folder is located strictly outside the public document root (`public/`).
- Direct URL hotlinking to raw PDF files on the web server is impossible.
- In `MediaResource`, badges distinguish private PDFs from public image assets.

---

## 4. Input Sanitization & Anti-Abuse Controls

### 4.1. Strict Mass-Assignment Protection
- All models use guarded fillable arrays (`$fillable`) ensuring foreign keys, hit counts, and administrative flags cannot be overwritten via request spoofing.

### 4.2. Review Spam Anonymization
- Reviews record sha256 hashes of client IP addresses (`ip_hash`) and user agents (`user_agent_hash`) rather than plaintext IP addresses. This provides privacy compliance while maintaining moderation and spam-tracking utility.

### 4.3. Anti-Circular Category Taxonomies
- Category schemas prevent cyclical hierarchy loops by dynamically excluding the current record ID from candidate parent categories.
