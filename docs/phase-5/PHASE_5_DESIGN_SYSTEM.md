# Phase 5 Design System & Tokens

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Design System Version:** 1.0.0  

---

## 1. Design Philosophy

The visual language is conceived as an **Editorial & Academic Digital Library**:
- **Credibility & Calm**: Replaces loud commercial gradients with clean paper surfaces, subtle borders, and generous whitespace.
- **Content First**: The book cover, title, author, and cultural context are the undisputed heroes of every layout.
- **Speed**: Pure semantic HTML and Tailwind CSS utilities; zero external JavaScript component libraries.
- **Bilingual Harmony**: Balanced visual weight between English Latin typography and Devanagari script.

---

## 2. Centralized Color Tokens

All colors are centralized in `tailwind.config.js`:

```js
colors: {
    brand: {
        50: '#fffbeb',
        100: '#fef3c7',
        200: '#fde68a',
        300: '#fcd34d',
        400: '#fbbf24',
        500: '#f59e0b',
        600: '#d97706',
        700: '#b45309', // Primary brand ochre / terracotta
        800: '#92400e', // Primary hover
        900: '#78350f',
        950: '#451a03',
    },
    library: {
        parchment: '#fdfbf7',
        surface: '#ffffff',
        muted: '#f8fafc',
        border: '#e2e8f0',
    },
}
```

### Semantic Color Roles
- `surface`: `#ffffff` (Card and modal surfaces)
- `surface-muted`: `#f8fafc` / `bg-slate-50` (Page background and subtle dividers)
- `border`: `#e2e8f0` / `border-slate-200` (Subtle card and section borders)
- `text-primary`: `#0f172a` / `text-slate-900` (Headings and primary text)
- `text-secondary`: `#334155` / `text-slate-700` (Body prose and navigation links)
- `text-muted`: `#64748b` / `text-slate-500` (Metadata, dates, and captions)
- `brand-700`: `#b45309` (Warm ochre / terracotta accent for library identity and primary actions)

---

## 3. Typography Tokens

### Font Stacks
```js
fontFamily: {
    sans: ['Inter', 'Noto Sans Devanagari', ...defaultTheme.fontFamily.sans],
}
```
- **Latin & Numbers**: `Inter` provides crisp clarity for numbers, ISBNs, and English UI labels.
- **Devanagari**: `Noto Sans Devanagari` ensures authentic rendering of Hindi conjuncts, matras, and ligatures without clipping.

### Line Height Strategy
In `resources/css/app.css`:
```css
body {
    line-height: 1.6;
}
```
Hindi Devanagari script requires relaxed vertical spacing to avoid collision between upper vowel signs (*matras*) and lower vowel signs (*u/oo matras*).

---

## 4. Spacing & Elevation

### Spacing Scale
- `p-3`, `p-4`, `p-5`: Card padding standards
- `py-12`, `py-16`, `py-20`: Section vertical rhythm
- `gap-4`: Standard grid gutters

### Book Cover Aspect Ratio & Elevation
```js
aspectRatio: {
    'book': '2 / 3',
},
boxShadow: {
    'book': '0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -2px rgba(0, 0, 0, 0.04)',
    'book-hover': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.05)',
}
```
Fixed `2/3` aspect ratio guarantees zero layout shifts (CLS) when covers load progressively.
