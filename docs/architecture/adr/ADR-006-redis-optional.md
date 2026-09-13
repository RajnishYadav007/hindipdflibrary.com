# ADR-006: Redis-Optional Architecture (Strict Cache & Queue Abstraction)

**Date:** September 13, 2026  
**Status:** Accepted  
**Deciders:** Lead Solutions Architect, Senior Laravel Engineer  

---

## Context
Redis is frequently cited as standard best practice for caching, queues, and sessions in modern Laravel applications. However, on shared hosting (StackCP), Redis is not verified and typically unavailable without dedicated server add-ons. Tightly coupling application logic to Redis would prevent deployment on shared hosting.

## Decision
Treat **Redis as completely OPTIONAL**. All caching, sessions, and queues must strictly use Laravel contracts and facades (`Cache`, `Queue`, `Session`). Direct calls to `Redis::` or `Predis` in application code are strictly forbidden.

## Reason
1. **Infrastructure Decoupling:** Allows running on `file` or `database` stores in V1, ensuring immediate production stability on shared hosting.
2. **Portability:** Code runs identically in local development, shared staging, or dedicated cloud clusters.
3. **No Code Rework:** When Redis becomes available in future hosting tiers, the transition requires changing only one `.env` parameter (`CACHE_STORE=redis`, `QUEUE_CONNECTION=redis`).

## Alternatives Considered
- **Mandate Redis in V1:** Rejected. Blocks deployment on current shared hosting.
- **Avoid Caching Completely:** Rejected. Causes unnecessary MySQL query load on high-traffic book pages.

## Consequences
- No reliance on Redis-exclusive features (e.g., Cache Tags via `Cache::tags(...)`). Invalidation must use key-based event listeners.
- Queue processing in V1 runs via MySQL `jobs` table rather than in-memory Redis lists.

## Future Migration Path
When upgrading to a VPS with Redis, configure `phpredis` extension, update `.env`, and immediately benefit from in-memory sub-millisecond speeds with zero code alterations.
