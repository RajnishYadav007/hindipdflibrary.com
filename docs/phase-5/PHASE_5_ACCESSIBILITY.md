# Phase 5 Accessibility & WCAG 2.2 AA Compliance

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Target:** WCAG 2.2 Level AA  

---

## 1. Landmarks & Semantic HTML

- **Header**: Enclosed in `<header>`, sticky with high contrast borders.
- **Navigation**: Explicit `<nav aria-label="Breadcrumb">` and main navigation menus.
- **Main**: Enclosed in `<main id="main-content">`.
- **Footer**: Enclosed in `<footer>`.
- **Search**: Contained in `<form role="search">` with `<label for="site-search-input" class="sr-only">Search Library</label>`.

---

## 2. Accessible Labeling & Text Equivalents

- **Buttons Without Visible Text**:
  - `<x-favorite-button>` provides `aria-label="Add to favorites"` or `aria-label="Remove from favorites"`.
  - Hamburger toggle provides `aria-label="Toggle navigation menu"` and `:aria-expanded="mobileMenuOpen"`.
- **Rating Stars**:
  - Wrapped with `aria-label="Rated 4.8 out of 5 stars (420 ratings)"` or `aria-label="No ratings yet"`. Decorative star SVGs are silenced with `aria-hidden="true"`.
- **Images**:
  - All cover images include descriptive `alt="Cover of {Title}"` attributes.

---

## 3. Focus Visibility & Keyboard Navigation

- **Global Focus Indicators**: Configured via `:focus-visible` outline in `resources/css/app.css`:
  ```css
  :focus-visible {
      outline: 2px solid #b45309;
      outline-offset: 2px;
  }
  ```
- **Keyboard Operable Dropdowns**: Language switcher and mobile drawer respond cleanly to keyboard focus, Esc dismissals, and outside clicks via Alpine.js primitives.
- **Color Contrast**: All primary text (`#0f172a` on `#ffffff`) exceeds a 12:1 contrast ratio; brand buttons (`#b45309` on `#ffffff`) exceed the 4.5:1 WCAG AA minimum.
