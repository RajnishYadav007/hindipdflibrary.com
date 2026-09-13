# ADR-003: Public Frontend via Blade + Livewire 4 + Tailwind CSS

**Date:** September 13, 2026  
**Status:** Accepted  
**Deciders:** Lead Solutions Architect, Senior Frontend Engineer  

---

## Context
A digital library platform requires rapid discovery: instant search filtering, dynamic category facets, bookmarking favorites, and pagination. Crucially, as an SEO-heavy content website, every book title, description, and review must be server-rendered for immediate Googlebot indexing.

## Decision
Adopt **Blade + Livewire 4 + Tailwind CSS** as the public frontend architecture.

## Reason
1. **Full SSR & SEO Dominance:** All initial page loads render 100% semantic HTML from the server. Search engines crawl the pages effortlessly with zero client-side JavaScript hydration delays.
2. **Reactivity Without SPA Complexity:** Livewire 4 delivers real-time UI interactivity (debounced search, rating stars, pagination, modal popups) without the overhead of client-side routing, Vue/React state management, or dual API maintenance.
3. **Shared Hosting Synergy:** Livewire runs purely inside PHP. There is no Node.js runtime required in production. Assets are compiled ahead-of-time via Vite.

## Alternatives Considered
- **React / Next.js / Vue (SPA):** High architectural complexity, requires maintaining separate REST/GraphQL APIs, risk of poor SEO indexation, and necessitates a Node server or complicated prerendering.
- **Pure Blade + Vanilla JS:** Clean and fast, but requires writing bespoke AJAX handling and DOM diffing for dynamic book filtering and reviews, which wastes development time.

## Consequences
- Requires Livewire 4 package (`livewire/livewire` v4.4.4).
- Frontend developers write PHP component logic instead of React/Vue stores.

## Future Migration Path
If mobile apps (iOS/Android) are built in the future, standard API controllers (`app/Http/Controllers/Api/...`) can be exposed alongside Livewire components.
