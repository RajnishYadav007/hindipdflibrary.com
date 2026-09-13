@props([
    'currentLocale' => app()->getLocale(),
    'locales' => [
        'hi' => ['name' => 'हिन्दी', 'native' => 'हिन्दी', 'label' => 'Hindi'],
        'en' => ['name' => 'English', 'native' => 'English', 'label' => 'English'],
        'sa' => ['name' => 'संस्कृतम्', 'native' => 'संस्कृतम्', 'label' => 'Sanskrit'],
    ],
])

<div class="relative inline-block text-left" x-data="{ open: false }">
    <button 
        type="button" 
        @click="open = !open" 
        @click.outside="open = false"
        aria-expanded="false" 
        aria-haspopup="true"
        aria-label="Select website language"
        class="inline-flex items-center gap-1.5 rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 transition focus-visible:outline-brand-700"
    >
        <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
        </svg>
        <span class="font-semibold">{{ $locales[$currentLocale]['name'] ?? 'Language' }}</span>
        <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-100" 
        x-transition:enter-start="transform opacity-0 scale-95" 
        x-transition:enter-end="transform opacity-100 scale-100" 
        x-transition:leave="transition ease-in duration-75" 
        x-transition:leave-start="transform opacity-100 scale-100" 
        x-transition:leave-end="transform opacity-0 scale-95" 
        class="absolute right-0 z-50 mt-1.5 w-44 origin-top-right rounded-lg border border-slate-200 bg-white shadow-lg focus:outline-none py-1 text-xs"
        style="display: none;"
    >
        <div class="px-3 py-1.5 text-[11px] font-semibold tracking-wider text-slate-400 uppercase border-b border-slate-100">
            Select Language
        </div>
        @foreach ($locales as $code => $data)
            <a 
                href="#locale-{{ $code }}" 
                @click="open = false"
                class="flex items-center justify-between px-3 py-2 text-slate-700 hover:bg-slate-50 transition {{ $currentLocale === $code ? 'bg-amber-50/60 font-semibold text-brand-900' : '' }}"
            >
                <span>{{ $data['name'] }}</span>
                <span class="text-[11px] text-slate-400">{{ $data['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>
