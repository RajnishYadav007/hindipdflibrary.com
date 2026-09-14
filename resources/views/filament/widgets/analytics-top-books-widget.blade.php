<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Book Performance & Editorial Intelligence
        </x-slot>
        <x-slot name="description">
            Admin rankings across views, reads, downloads, community favorites, and ratings.
        </x-slot>

        <div class="space-y-4">
            {{-- Filter Pills / Tabs --}}
            <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-3 dark:border-gray-800">
                @php
                    $tabs = [
                        'most_viewed' => 'Most Viewed',
                        'most_read' => 'Most Read',
                        'most_downloaded' => 'Most Downloaded',
                        'most_favorited' => 'Most Favorited',
                        'most_rated' => 'Most Rated',
                        'highest_rated' => 'Highest Rated',
                        'trending' => 'Trending (7-Day)',
                    ];
                @endphp

                @foreach ($tabs as $key => $label)
                    <button
                        wire:click="setRankingType('{{ $key }}')"
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors {{ $rankingType === $key ? 'bg-amber-600 text-white dark:bg-amber-500' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Ranked Books Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 dark:bg-gray-800/50 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-2.5 w-12">#</th>
                            <th scope="col" class="px-4 py-2.5">Book Title</th>
                            <th scope="col" class="px-4 py-2.5">Author(s)</th>
                            <th scope="col" class="px-4 py-2.5">Community Rating</th>
                            <th scope="col" class="px-4 py-2.5 text-right">Metric</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 border-t border-gray-100 dark:divide-gray-800 dark:border-gray-800">
                        @php $books = $this->getBooks(); @endphp
                        @forelse ($books as $index => $book)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                                <td class="px-4 py-3 font-mono font-medium text-gray-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                    {{ $book['title'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    {{ $book['authors'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1 rounded bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-950/30 dark:text-amber-400">
                                        {{ $book['rating'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-amber-600 dark:text-amber-400">
                                    {{ $book['metric_value'] }}
                                    <span class="block text-xs font-normal text-gray-400">{{ $book['metric_label'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                    No published books recorded for this metric yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
