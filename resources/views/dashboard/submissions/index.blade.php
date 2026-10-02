<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex text-xs text-slate-500 mb-1" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-amber-800">Dashboard</a>
                    <span class="mx-2">/</span>
                    <span class="text-slate-700 font-medium">मेरी प्रस्तुतियां (My Submissions)</span>
                </nav>
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight">
                    मेरी प्रस्तुतियां व समीक्षा स्थिति (Submissions &amp; Status)
                </h2>
            </div>

            <!-- Fast Action Dropdown or Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('dashboard.submissions.audiobook.create') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                    + ऑडियोबुक सबमिट करें
                </a>
                <a href="{{ route('dashboard.submissions.chalisa.create') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                    + चालीसा जोड़ें
                </a>
                <a href="{{ route('dashboard.submissions.aarti.create') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                    + आरती जोड़ें
                </a>
                <a href="{{ route('dashboard.submissions.story.create') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                    + बाल कथा सबमिट करें
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- 1. Audiobooks Section -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        प्रस्तुत ऑडियोबुक्स (Audiobooks)
                        <span class="text-xs font-normal text-slate-500">({{ $audiobooks->count() }})</span>
                    </h3>
                    <a href="{{ route('dashboard.submissions.audiobook.create') }}" class="text-xs text-amber-700 hover:text-amber-800 font-semibold">+ नई ऑडियोबुक</a>
                </div>

                @if($audiobooks->isEmpty())
                    <p class="text-sm text-slate-500 py-4 text-center">आपने अभी तक कोई ऑडियोबुक सबमिट नहीं की है।</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-100">
                                    <th class="py-2.5">शीर्षक (Title)</th>
                                    <th class="py-2.5">स्वर (Narrator)</th>
                                    <th class="py-2.5">स्थिति (Status)</th>
                                    <th class="py-2.5">एडमिन समीक्षा टिप्पणी (Notes)</th>
                                    <th class="py-2.5">दिनांक (Date)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($audiobooks as $ab)
                                    <tr>
                                        <td class="py-3 font-semibold text-slate-900">{{ $ab->title }}</td>
                                        <td class="py-3 text-slate-600">{{ $ab->narrator ?: '—' }}</td>
                                        <td class="py-3">
                                            @if($ab->status === 'published')
                                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">प्रकाशित (Published)</span>
                                            @elseif($ab->status === 'pending_review')
                                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">समीक्षाधीन (Under Review)</span>
                                            @elseif($ab->status === 'rejected')
                                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800">अस्वीकृत (Rejected)</span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">{{ $ab->status }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-slate-600 max-w-xs">
                                            {{ $ab->admin_notes ?: '—' }}
                                        </td>
                                        <td class="py-3 text-slate-400 font-mono">{{ $ab->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- 2. Chalisa & Aarti Section -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                        प्रस्तुत चालीसा व आरती (Devotional Content)
                        <span class="text-xs font-normal text-slate-500">({{ $devotionals->count() }})</span>
                    </h3>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('dashboard.submissions.chalisa.create') }}" class="text-xs text-amber-700 hover:text-amber-800 font-semibold">+ चालीसा</a>
                        <a href="{{ route('dashboard.submissions.aarti.create') }}" class="text-xs text-orange-700 hover:text-orange-800 font-semibold">+ आरती</a>
                    </div>
                </div>

                @if($devotionals->isEmpty())
                    <p class="text-sm text-slate-500 py-4 text-center">आपने अभी तक कोई चालीसा या आरती सबमिट नहीं की है।</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-100">
                                    <th class="py-2.5">प्रकार</th>
                                    <th class="py-2.5">शीर्षक (Title)</th>
                                    <th class="py-2.5">देवता (Deity)</th>
                                    <th class="py-2.5">स्थिति (Status)</th>
                                    <th class="py-2.5">समीक्षा टिप्पणी</th>
                                    <th class="py-2.5">दिनांक</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($devotionals as $dev)
                                    <tr>
                                        <td class="py-3">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $dev->type === 'chalisa' ? 'bg-amber-100 text-amber-900' : 'bg-orange-100 text-orange-900' }}">
                                                {{ $dev->type }}
                                            </span>
                                        </td>
                                        <td class="py-3 font-semibold text-slate-900">{{ $dev->title }}</td>
                                        <td class="py-3 text-slate-600">{{ $dev->deity_name }}</td>
                                        <td class="py-3">
                                            @if($dev->status === 'published')
                                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">प्रकाशित</span>
                                            @elseif($dev->status === 'pending_review')
                                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">समीक्षाधीन</span>
                                            @elseif($dev->status === 'rejected')
                                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800">अस्वीकृत</span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">{{ $dev->status }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-slate-600 max-w-xs">{{ $dev->admin_notes ?: '—' }}</td>
                                        <td class="py-3 text-slate-400 font-mono">{{ $dev->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- 3. Children's Stories Section -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        प्रस्तुत बाल कथाएं (Children's Stories)
                        <span class="text-xs font-normal text-slate-500">({{ $stories->count() }})</span>
                    </h3>
                    <a href="{{ route('dashboard.submissions.story.create') }}" class="text-xs text-emerald-700 hover:text-emerald-800 font-semibold">+ नई बाल कथा</a>
                </div>

                @if($stories->isEmpty())
                    <p class="text-sm text-slate-500 py-4 text-center">आपने अभी तक कोई बाल कथा सबमिट नहीं की है।</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-100">
                                    <th class="py-2.5">श्रृंखला शीर्षक (Series Title)</th>
                                    <th class="py-2.5">आयु वर्ग (Age Group)</th>
                                    <th class="py-2.5">अध्याय संख्या (Episodes)</th>
                                    <th class="py-2.5">स्थिति (Status)</th>
                                    <th class="py-2.5">समीक्षा टिप्पणी</th>
                                    <th class="py-2.5">दिनांक</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($stories as $st)
                                    <tr>
                                        <td class="py-3 font-semibold text-slate-900">{{ $st->title }}</td>
                                        <td class="py-3 text-slate-600">{{ $st->age_group }} वर्ष</td>
                                        <td class="py-3 text-slate-600">{{ $st->episodes->count() }}</td>
                                        <td class="py-3">
                                            @if($st->status === 'published')
                                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">प्रकाशित</span>
                                            @elseif($st->status === 'pending_review')
                                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">समीक्षाधीन</span>
                                            @elseif($st->status === 'rejected')
                                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800">अस्वीकृत</span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">{{ $st->status }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-slate-600 max-w-xs">{{ $st->admin_notes ?: '—' }}</td>
                                        <td class="py-3 text-slate-400 font-mono">{{ $st->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
