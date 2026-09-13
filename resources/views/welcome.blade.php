@extends('layouts.base')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 text-center">
    <div class="mx-auto max-w-2xl">
        <h1 class="text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">
            Hindi PDF Library
        </h1>
        <p class="mt-4 text-lg text-slate-600">
            Digital book discovery & content platform foundation.
        </p>
        <div class="mt-8 flex justify-center gap-4">
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-md bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-amber-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600 transition">
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="rounded-md bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-amber-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600 transition">
                    Get Started
                </a>
                <a href="{{ route('login') }}" class="rounded-md border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 transition">
                    Sign In
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
