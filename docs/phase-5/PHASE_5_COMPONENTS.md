# Phase 5 Component Usage & Contracts

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  

---

## 1. BookCard (`<x-book-card>`)

### Usage
```blade
{{-- Using Eloquent Book Model --}}
<x-book-card :book="$book" />

{{-- Using Standalone Props --}}
<x-book-card 
    title="गोदान (Godan)" 
    author="मुंशी प्रेमचंद" 
    category="उपन्यास" 
    language="hi" 
    year="1936" 
    rating="4.8" 
    ratingCount="420"
    :coverUrl="asset('images/covers/godan.webp')" 
/>
```

### Key Props
- `book`: Optional `App\Models\Book` instance (automatically resolves relations).
- `title`: String book title.
- `author`: String author name.
- `coverUrl`: Nullable string image URL (renders elegant SVG fallback when null).
- `language`: String ISO code (default `hi`).
- `rating`: Float rating score (`0.00` to `5.00`).
- `ratingCount`: Integer review count.
- `year`: Integer publication year.

---

## 2. RatingStars (`<x-rating-stars>`)

### Usage
```blade
{{-- Populated Rating --}}
<x-rating-stars rating="4.5" ratingCount="120" size="sm" />

{{-- Empty State (No Ratings Yet) --}}
<x-rating-stars :rating="0" />
```

### Accessibility
Outputs `aria-label="Rated 4.5 out of 5 stars (120 ratings)"` or `aria-label="No ratings yet"`. Star SVGs are marked with `aria-hidden="true"`.

---

## 3. FavoriteButton (`<x-favorite-button>`)

### Usage
```blade
<x-favorite-button :isFavorited="false" size="sm" />
```

### Accessibility
Outputs `aria-label="Add to favorites"` when unfavorited and `aria-label="Remove from favorites"` when favorited.

---

## 4. BookMetadata (`<x-book-metadata>`)

### Usage
```blade
{{-- From Eloquent Model --}}
<x-book-metadata :book="$book" />

{{-- From Custom Array --}}
<x-book-metadata :metadata="[
    'Author' => 'Munshi Premchand',
    'Language' => 'Hindi',
    'Publication Year' => '1936',
    'Pages' => '368 pages',
]" />
```

### Semantic Structure
Renders an accessible `<dl>`, `<dt>`, `<dd>` definition list and automatically suppresses empty attributes.

---

## 5. Breadcrumb (`<x-breadcrumb>`)

### Usage
```blade
<x-breadcrumb :items="[
    ['label' => 'Books', 'url' => '/books'],
    ['label' => 'Premchand', 'url' => '/authors/premchand'],
    ['label' => 'Godaan'],
]" />
```

### Semantic Structure
Renders `<nav aria-label="Breadcrumb">` wrapping an ordered list `<ol>` with `aria-current="page"` on the final item.
