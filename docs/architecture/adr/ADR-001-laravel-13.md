# ADR-001: Adoption of Laravel 13 Framework

**Date:** September 13, 2026  
**Status:** Accepted  
**Deciders:** Lead Solutions Architect, Senior Laravel Engineer  

---

## Context
Hindi PDF Library is migrating from an aging WordPress site to a custom, modern digital library platform. We require an enterprise-grade, robust, elegant PHP framework capable of handling complex relational data (books, authors, translations, reviews), rich developer tooling, modern security, and long-term maintainability.

## Decision
Adopt **Laravel 13** (`laravel/framework` ^13.0) as the core backend foundation.

## Reason
1. **PHP 8.4 Support:** Both our local environment (PHP 8.5.8) and production hosting (PHP 8.4.25) support modern PHP versions required by Laravel 13 (^8.3).
2. **Ecosystem Synergy:** Livewire 4, Filament 5, and Spatie libraries all provide official first-class support for Laravel 13.
3. **Enterprise Architecture:** Built-in abstraction layers for routing, caching, queues, filesystems, and testing ensure clean separation of concerns.

## Alternatives Considered
- **Remain on WordPress:** Inadequate for custom relational library modeling, slow under heavy data, vulnerable to plugin bloat and security exploits.
- **Laravel 11 / 12:** Older releases; starting a greenfield project on an older major version incurs immediate technical debt.
- **Symfony / Slim:** Highly modular but lacks the cohesive, rapid-development ecosystem (Filament, Livewire, Breeze, Socialite) required for fast, robust delivery.

## Consequences
- Requires PHP ^8.3 runtime (verified active on both environments).
- Long-term support and active security patching guaranteed.

## Future Migration Path
Standard yearly Laravel major upgrades via `composer update` and automated Laravel Shift.
