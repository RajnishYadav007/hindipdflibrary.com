<x-filament-widgets::widget>
    @php
        $overview = $this->getOverview();
        $popular = $this->getPopularSearches();
        $zeroResults = $this->getZeroResultSearches();
    @endphp

    <x-filament::section>
        <x-slot name="heading">
            Search Intelligence & Discovery Analytics
        </x-slot>
        <x-slot name="description">
            High-value discovery intelligence: search volume, zero-result keyword gaps, and reader click-through rates.
        </x-slot>

        <div class="space-y-6">
            {{-- Overview Metric Cards --}}
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-800/30">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Searches (30d)</span>
                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ number_format($overview['total_searches']) }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-800/30">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Book Result Clicks</span>
                    <p class="mt-1 text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($overview['total_clicks']) }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-800/30">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Overall Search CTR</span>
                    <p class="mt-1 text-xl font-bold text-amber-600 dark:text-amber-400">{{ $overview['overall_ctr'] }}%</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-800/30">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Zero-Result Searches</span>
                    <p class="mt-1 text-xl font-bold text-rose-600 dark:text-rose-400">{{ number_format($overview['zero_result_searches']) }}</p>
                </div>
            </div>

            {{-- Dual Columns: Popular vs Zero-Result Tables --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {{-- Popular Searches --}}
                <div class="space-y-3">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        Popular Search Queries
                    </h4>
                    <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-800">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-500">
                                <tr>
                                    <th class="px-3 py-2">Query</th>
                                    <th class="px-3 py-2 text-right">Volume</th>
                                    <th class="px-3 py-2 text-right">Clicks</th>
                                    <th class="px-3 py-2 text-right">CTR</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($popular as $item)
                                    <tr>
                                        <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">"{{ $item['query'] }}"</td>
                                        <td class="px-3 py-2 text-right font-mono text-gray-600 dark:text-gray-300">{{ number_format($item['count']) }}</td>
                                        <td class="px-3 py-2 text-right font-mono text-emerald-600 dark:text-emerald-400">{{ number_format($item['clicks']) }}</td>
                                        <td class="px-3 py-2 text-right font-semibold text-amber-600 dark:text-amber-400">{{ $item['ctr'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-4 text-center text-gray-400">No popular queries recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Zero-Result Searches (Catalog Acquisition Signals) --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Zero-Result Gaps (Acquisition Signals)
                        </h4>
                        <span class="text-[10px] text-gray-400">Books requested but not found</span>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-800">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-500">
                                <tr>
                                    <th class="px-3 py-2">Missing Search Query</th>
                                    <th class="px-3 py-2 text-right">Missed Requests</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($zeroResults as $zero)
                                    <tr>
                                        <td class="px-3 py-2 font-medium text-rose-600 dark:text-rose-400">"{{ $zero['query'] }}"</td>
                                        <td class="px-3 py-2 text-right font-mono text-gray-600 dark:text-gray-300">{{ number_format($zero['count']) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-3 py-4 text-center text-gray-400">Zero search result gaps detected.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
