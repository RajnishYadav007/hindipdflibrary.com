# Phase 5 Responsive Design & Breakpoints

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  

---

## 1. Breakpoint Hierarchy

The layout scales intentionally across all standard viewport widths:

| Device Class | Viewport Range | Grid Columns (BookCard) | Header Behavior |
| :--- | :--- | :--- | :--- |
| **Small Mobile** | `320px – 375px` | 2 columns (`gap-3`) | Compact header, mobile search row below logo |
| **Large Mobile** | `376px – 639px` | 2 columns (`gap-4`) | Mobile search row, drawer navigation |
| **Tablet** | `640px – 767px` | 3 columns | Responsive container margins |
| **Medium Tablet / Small Laptop** | `768px – 1023px`| 4 columns | Centered search bar visible in header |
| **Desktop** | `1024px – 1279px`| 6 columns | Full horizontal navigation and language dropdown |
| **Large Desktop / HiDPI** | `1280px – 1920px`| 6 columns | Centered `max-w-7xl` container prevents overstretching |

---

## 2. Touch Targets & Mobile Ergonomics

- **Minimum Touch Target**: All clickable elements (favorite button, mobile menu toggle, language switcher) maintain a minimum hit boundary of `40px` to `48px`.
- **Zero Horizontal Overflow**: All flex wrappers and grids enforce `min-w-0` and text clamping to prevent layout breakage on narrow 320px devices.
- **Mobile Search Placement**: On small screens (`< 768px`), search input is placed immediately beneath the primary brand row in an accessible sticky sub-bar.
