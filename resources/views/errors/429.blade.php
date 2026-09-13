@extends('layouts.base', ['title' => '429 - Too Many Requests'])

@section('content')
<div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8 text-center">
    <p class="text-base font-semibold text-amber-600">429</p>
    <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-5xl">Too Many Requests</h1>
    <p class="mt-4 text-base text-slate-600">You have made too many requests in a short period. Please wait a moment and try again.</p>
    <div class="mt-6">
        <a href="{{ url('/') }}" class="rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-xs hover:bg-amber-500 transition">
            Back to Home
        </a>
    </div>
</div>
@endsection
