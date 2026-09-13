# ADR-002: Shared-Hosting-First Architecture with Zero-Lock-In Upgrade Path

**Date:** September 13, 2026  
**Status:** Accepted  
**Deciders:** Lead Solutions Architect, DevOps Engineer  

---

## Context
The production environment is a shared cloud hosting account (StackCP / ServerByt). Shared hosting imposes concrete resource limits: no root access, no persistent daemon processes, no Supervisor, no Docker, and no native Redis. Designing an architecture that assumes a dedicated Kubernetes or VPS cluster would cause immediate operational failure in production.

## Decision
Design the entire V1 architecture to be **100% operational on shared hosting**, while coding strictly against Laravel service contracts so that future VPS/Cloud migration requires **only `.env` changes with zero code refactoring**.

## Reason
1. **Production Reality:** Avoids spending client budget on premature VPS infrastructure before traffic and monetization justify it.
2. **Operational Feasibility:** Ensures the application runs reliably on Apache + PHP-FPM using native MySQL 8 for relational data, queues, and sessions.
3. **Graceful Degradation:** All advanced features (queues, search, media conversions) run via ephemeral cron triggers without hanging.

## Alternatives Considered
- **Mandate VPS / Docker Immediately:** Rejected. Imposes unnecessary financial overhead and deployment complexity on Phase 0.
- **Compromise Code Quality for Shared Hosting (Spaghetti PHP):** Rejected. We adhere to PSR-12, clean Eloquent models, and Laravel architectural contracts.

## Consequences
- No long-running CLI workers in V1; queue processing relies on scheduled `--stop-when-empty` runs.
- Background tasks execute in short batches (< 50 seconds).

## Future Migration Path
When traffic exceeds 50,000 daily users, spin up a Linux VPS (e.g., Hetzner / AWS), configure Supervisor + Redis, point the `.env` to the new services, and migrate seamlessly.
