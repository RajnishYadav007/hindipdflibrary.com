<!-- Mobile Bottom Navigation Bar (Visible strictly below md viewport matching reference) -->
<nav 
    aria-label="Mobile Navigation"
    class="fixed bottom-0 left-0 right-0 z-40 border-t border-warm-200/90 bg-white/95 backdrop-blur-md md:hidden shadow-lg pb-safe"
>
    <div class="grid grid-cols-5 h-14 items-center text-center">
        <!-- 1. Home (होम) -->
        <a 
            href="{{ route('home') }}" 
            class="flex flex-col items-center justify-center py-1 {{ request()->routeIs('home', 'home.root') ? 'text-terracotta-600 font-bold' : 'text-slate-500 hover:text-slate-900' }} transition"
        >
            <svg class="h-5 w-5 {{ request()->routeIs('home', 'home.root') ? 'stroke-2' : 'stroke-1.5' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            <span class="text-[10px] mt-0.5 tracking-tight">होम</span>
        </a>

        <!-- 2. Books (पुस्तकें) -->
        <a 
            href="{{ route('books.index') }}" 
            class="flex flex-col items-center justify-center py-1 {{ request()->routeIs('books.*') && !request()->routeIs('hanuman-chalisa*') ? 'text-terracotta-600 font-bold' : 'text-slate-500 hover:text-slate-900' }} transition"
        >
            <svg class="h-5 w-5 {{ request()->routeIs('books.*') && !request()->routeIs('hanuman-chalisa*') ? 'stroke-2' : 'stroke-1.5' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
            <span class="text-[10px] mt-0.5 tracking-tight">पुस्तकें</span>
        </a>

        <!-- 3. Audio (ऑडियो) -->
        <a 
            href="#audiobook-hub" 
            class="flex flex-col items-center justify-center py-1 text-slate-500 hover:text-slate-900 transition"
        >
            <svg class="h-5 w-5 stroke-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
            </svg>
            <span class="text-[10px] mt-0.5 tracking-tight">ऑडियो</span>
        </a>

        <!-- 4. Chalisa (चालीसा) -->
        <a 
            href="{{ route('hanuman-chalisa') }}" 
            class="flex flex-col items-center justify-center py-1 {{ request()->routeIs('hanuman-chalisa*') ? 'text-terracotta-600 font-bold' : 'text-slate-500 hover:text-slate-900' }} transition"
        >
            <svg class="h-5 w-5 {{ request()->routeIs('hanuman-chalisa*') ? 'stroke-2' : 'stroke-1.5' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5" />
            </svg>
            <span class="text-[10px] mt-0.5 tracking-tight">चालीसा</span>
        </a>

        <!-- 5. Children (बच्चों के लिए) -->
        <a 
            href="#children-stories" 
            class="flex flex-col items-center justify-center py-1 text-slate-500 hover:text-slate-900 transition"
        >
            <svg class="h-5 w-5 stroke-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
            </svg>
            <span class="text-[10px] mt-0.5 tracking-tight">बच्चों के लिए</span>
        </a>
    </div>
</nav>