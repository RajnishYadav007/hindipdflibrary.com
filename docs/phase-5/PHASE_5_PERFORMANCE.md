# Phase 5 Performance & Core Web Vitals

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  

---

## 1. Asset Footprint

Compiled asset metrics via Vite:
- **Compiled CSS (`app.css`)**: `62.41 kB` raw (`10.65 kB` gzipped).
- **Compiled JS (`app.js`)**: `51.52 kB` raw (`19.51 kB` gzipped).
- **Total First-Load Payload**: Under `31 kB` gzipped.

---

## 2. Cumulative Layout Shift (CLS) Mitigation

1. **Fixed 2:3 Aspect Ratio**:
   - `aspect-[2/3]` is applied to all cover containers. Space is reserved in the DOM before images load, completely preventing layout shifts.
2. **Lazy Loading**:
   - Covers below the fold use native browser `loading="lazy"`.
3. **SVG Fallbacks**:
   - When covers are not present, an internal inline SVG cover renders instantly with zero network request overhead.

---

## 3. Font Loading Optimization

- **Bunny Fonts CDN**: Privacy-friendly, fast CDN serving `Inter` + `Noto Sans Devanagari`.
- **`display=swap`**: Enables instant text rendering using system sans-serif fallback while web fonts stream in, avoiding Flash of Invisible Text (FOIT).
- **Restrained Weights**: Limited strictly to 400, 500, 600, 700 to keep font payload minimal.
