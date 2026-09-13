# ADR-004: Administration & CMS Architecture via Filament 5

**Date:** September 13, 2026  
**Status:** Accepted  
**Deciders:** Lead Solutions Architect, Senior Laravel Engineer  

---

## Context
Managing a digital library catalog requires a feature-rich admin dashboard: CRUD interfaces for books, authors, categories, tags, PDF uploads, legal copyright auditing, user review moderation, and traffic analytics. Building custom admin panels from scratch consumes 40-50% of total engineering time.

## Decision
Adopt **Filament 5** (`filament/filament` v5.8.1) as the administration engine.

## Reason
1. **Native TALL Stack Integration:** Filament is built directly on Tailwind CSS, Alpine.js, and Livewire. It shares the exact same tech stack as our public frontend.
2. **First-Party Ecosystem Plugins:** Natively integrates with Spatie Permission (RBAC) and Spatie Media Library (cover thumbnail uploads).
3. **Productivity:** Provides out-of-the-box table filtering, search, bulk actions, CSV export, relation managers, and dashboard analytics widgets.

## Alternatives Considered
- **Custom Blade Admin:** High maintenance, repetitive CRUD code, slow delivery.
- **Nova:** Commercial paid license ($99-$299/project), proprietary, less extensible than Filament.
- **Voyager / Backpack:** Less active, older paradigms, lack of Livewire/Tailwind synergy.

## Consequences
- Requires Filament 5 dependencies.
- Shared hosting OPcache must remain enabled to optimize compiled Blade/Filament classes.
- Filament admin route will be configured on a custom path (e.g., `/portal-control-hpl/`) for security obfuscation.

## Future Migration Path
Filament's modular panel architecture allows spinning up multi-panel systems (e.g., an Author Portal or Reviewer Portal) in the future with zero architectural rewrite.
