# Workstream P: Git & Repository Standards

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Auditor:** Lead Solutions Architect & DevOps Engineer  

---

## 1. Executive Summary

Repository standards ensure version history integrity, prevent accidental data leaks, and establish reproducible release workflows across developers and AI engineering agents.

---

## 2. Commit & Branching Standards

### 2.1. Branching Model (Trunk-Based with Protected Main)
- `main`: Production-ready branch. Direct pushes are protected. Only merged via tested pull requests or audited release tags.
- `staging`: Staging branch deployed directly to `staging.hindipdflibrary.com`.
- `feat/feature-name`: Feature development branches.
- `fix/bug-name`: Bug fix branches.
- `chore/task-name`: Architecture, tooling, and documentation tasks.

### 2.2. Conventional Commit Messages
Commits must adhere strictly to conventional commit specifications:
- `feat(books): add Devanagari title transliteration to Book model`
- `fix(auth): correct Google OAuth callback redirect loop`
- `docs(arch): update queue strategy for shared hosting cron worker`
- `perf(search): add composite fulltext index on books table`
- `security(uploads): enforce MIME magic byte verification on PDFs`

---

## 3. Strict Exclusions & Leak Prevention

The `.gitignore` configuration explicitly excludes:
1. **Secrets & Keys:** `.env`, `.env.production`, `auth.json`, OAuth secrets, private SSL certificates.
2. **Third-Party Dependencies:** `vendor/`, `node_modules/`.
3. **Dynamic Application State:** `storage/logs/`, `storage/framework/cache/`, `storage/framework/sessions/`, `storage/framework/views/`.
4. **Local Database & Dump Files:** `*.sql`, `*.sqlite`, database backup archives.
5. **Symlinked Public Media:** `public/storage`, `public/hot`.

> [!WARNING]
> If a developer or AI agent accidentally commits an `.env` file or API token, the token is deemed **instantly compromised** and must be rotated immediately. Git history rewriting (`git filter-branch` or BFG) must be executed before pushing to remotes.
