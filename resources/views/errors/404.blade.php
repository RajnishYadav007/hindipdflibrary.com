@extends('layouts.base', ['title' => '404 - Page Not Found'])

@section('content')
<div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8 text-center">
    <p class="text-base font-semibold text-amber-600">404</p>
    <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-5xl">Page Not Found</h1>
    <p class="mt-4 text-base text-slate-600">Sorry, we couldn’t find the page or book you’re looking for.</p>
    <div class="mt-6">
        <a href="{{ url('/') }}" class="rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-xs hover:bg-amber-500 transition">
            Back to Home
        </a>
    </div>
</div>
@endsection
