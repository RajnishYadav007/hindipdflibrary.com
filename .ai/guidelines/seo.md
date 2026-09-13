# AI Guidelines: SEO & Multilingual Discovery

## 1. Canonical URLs & Trailing Slashes
- Enforce lowercase canonical URLs without trailing slashes.
- Every public page must emit `<link rel="canonical" href="...">`.

## 2. Multilingual Tagging (`hreflang`)
- For pages with dual English and Hindi representations, emit proper alternate hreflang tags:
  ```html
  <link rel="alternate" hreflang="en" href="https://hindipdflibrary.com/en/books/godan" />
  <link rel="alternate" hreflang="hi" href="https://hindipdflibrary.com/hi/books/godan" />
  <link rel="alternate" hreflang="x-default" href="https://hindipdflibrary.com/books/godan" />
  ```

## 3. Schema.org JSON-LD Structured Data
- Include rich Book markup on all single-book display templates:
  ```html
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Book",
    "name": "{{ $book->title }}",
    "author": {
      "@type": "Person",
      "name": "{{ $book->author->name }}"
    },
    "isbn": "{{ $book->isbn }}",
    "inLanguage": "{{ $book->language_code }}",
    "numberOfPages": {{ $book->page_count ?? 'null' }}
  }
  </script>
  ```

## 4. WordPress Migration 301 Redirect Rules
- Never break existing WordPress indexed permalinks.
- Preserve legacy URLs via high-performance database-cached 301 redirects.
