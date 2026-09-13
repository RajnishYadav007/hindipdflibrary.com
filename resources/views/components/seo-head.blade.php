@props([
    'seo' => null,
    'title' => null,
])

@if ($seo instanceof \App\Support\SEO\SeoData)
    <title>{{ $seo->title }}</title>
    <meta name="description" content="{{ $seo->description }}">
    <meta name="robots" content="{{ $seo->robots }}">
    <link rel="canonical" href="{{ $seo->canonicalUrl }}">

    {{-- Reciprocal Hreflang Alternates --}}
    @foreach ($seo->hreflangs as $alternate)
        <link rel="alternate" hreflang="{{ $alternate['locale'] }}" href="{{ $alternate['url'] }}">
    @endforeach

    {{-- Open Graph Social Metadata --}}
    <meta property="og:title" content="{{ $seo->ogTitle ?? $seo->title }}">
    <meta property="og:description" content="{{ $seo->ogDescription ?? $seo->description }}">
    <meta property="og:url" content="{{ $seo->ogUrl ?? $seo->canonicalUrl }}">
    <meta property="og:type" content="{{ $seo->ogType }}">
    <meta property="og:site_name" content="{{ config('app.name', 'Hindi PDF Library') }}">
    <meta property="og:locale" content="{{ $seo->locale === 'hi' ? 'hi_IN' : 'en_US' }}">
    @if ($seo->ogImage)
        <meta property="og:image" content="{{ $seo->ogImage }}">
    @endif

    {{-- Twitter / X Metadata --}}
    <meta name="twitter:card" content="{{ $seo->twitterCard }}">
    <meta name="twitter:title" content="{{ $seo->twitterTitle ?? $seo->title }}">
    <meta name="twitter:description" content="{{ $seo->twitterDescription ?? $seo->description }}">
    @if ($seo->twitterImage)
        <meta name="twitter:image" content="{{ $seo->twitterImage }}">
    @endif

    {{-- JSON-LD Structured Data --}}
    @foreach ($seo->structuredData as $schema)
        <script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
        </script>
    @endforeach
@else
    <title>{{ $title ?? config('app.name', 'Hindi PDF Library') }}</title>
@endif
