# Phase 6 — Accessibility & Semantic Web Standards (WCAG 2.1 AA)

## 1. Semantic Hierarchy
- Every public page provides a single `<h1>` tag indicating the primary document subject.
- Subsections use ordered heading tiers (`<h2>`, `<h3>`) maintaining an accessible outline for screen reader navigation.
- Main landmark (`<main id="main-content">`) is preserved with skip navigation links in `layouts.base`.

## 2. Forms & Controls
- Search inputs include explicit `<label for="..." class="sr-only">` tags and `role="search"` on search forms.
- Buttons and links expose descriptive `aria-label` attributes where text is omitted or visual-only.
- Interactive collapse components (e.g., FAQ accordions) provide keyboard-operable buttons and explicit expand/collapse indicators.

## 3. High-Contrast Focus States & Color Safety
- Interactive elements feature explicit `focus-visible:outline-2 focus-visible:outline-brand-700` rings.
- All typography combinations adhere to WCAG 2.1 AA contrast ratio requirements (minimum 4.5:1 for body copy).
- Hindi Devanagari text rendered via `Noto Sans Devanagari` preserves generous line-height (`1.6`) avoiding matra clipping.
