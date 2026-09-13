# Phase 5 Architecture Decision Records (ADRs)

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Phase:** Phase 5 — Public UI / Design System  

---

## ADR 01: Blade Components for Presentation Primitives

- **Context:** The public frontend requires high-performance, reusable cards, buttons, badges, and layout blocks.
- **Decision:** Build reusable Blade components (`resources/views/components/`) rather than Livewire components for all display-only elements.
- **Consequences:** Maximizes server-side caching performance, reduces client-side payload, and provides optimal Google SEO indexability.

---

## ADR 02: Dual Typography Stack for Bilingual Readability

- **Context:** The library serves English and Hindi text simultaneously. Hindi Devanagari script features complex vowel signs and ligatures that render poorly with generic Western fonts.
- **Decision:** Load `Inter` for Latin characters and numbers, paired with `Noto Sans Devanagari` for Hindi text, with global `line-height: 1.6` to eliminate glyph clipping.
- **Consequences:** Flawless Devanagari presentation, high numerical clarity, and instant font swapping via `display=swap`.

---

## ADR 03: Fixed 2:3 Aspect Ratio for Book Covers

- **Context:** Digital book covers from various publishers have varying aspect ratios, causing layout shifts as images load.
- **Decision:** Standardize all book card cover containers to `aspect-[2/3]` using Tailwind's aspect-ratio utility and `object-cover`.
- **Consequences:** Eliminates Cumulative Layout Shift (CLS) completely and gives the digital library a unified, polished bookshelf appearance.

---

## ADR 04: Graceful SVG Cover Fallbacks

- **Context:** Many historical public domain titles may not have scanned jacket covers available initially.
- **Decision:** Render an internal styled SVG fallback book cover featuring the title in serif typography, author name, and library emblem.
- **Consequences:** Eliminates broken image icons, removes placeholder image HTTP requests, and preserves high visual aesthetic even for unjacketed archival books.
