@extends('layouts.base', ['title' => '403 - Forbidden'])

@section('content')
<div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8 text-center">
    <p class="text-base font-semibold text-rose-600">403</p>
    <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-5xl">Access Denied</h1>
    <p class="mt-4 text-base text-slate-600">You do not have permission to access this resource or administrative area.</p>
    <div class="mt-6">
        <a href="{{ url('/') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-xs hover:bg-slate-800 transition">
            Back to Home
        </a>
    </div>
</div>
@endsection
