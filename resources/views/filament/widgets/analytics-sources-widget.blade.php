<x-filament-widgets::widget>
    @php
        $data = $this->getBreakdown();
        $sources = $data['sources'];
        $devices = $data['devices'];
        $countries = $data['countries'];

        $totalSourceViews = array_sum($sources) ?: 1;
        $totalDeviceViews = array_sum($devices) ?: 1;
        $totalCountryViews = array_sum($countries) ?: 1;
    @endphp

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        {{-- Traffic Sources --}}
        <x-filament::section>
            <x-slot name="heading">
                Traffic Sources (30d)
            </x-slot>

            <div class="space-y-3">
                @forelse ($sources as $source => $count)
                    @php $pct = round(($count / $totalSourceViews) * 100); @endphp
                    <div>
                        <div class="flex justify-between text-xs font-medium">
                            <span class="capitalize text-gray-700 dark:text-gray-300">{{ $source }}</span>
                            <span class="text-gray-500 dark:text-gray-400">{{ number_format($count) }} ({{ $pct }}%)</span>
                        </div>
                        <div class="mt-1 h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-2 rounded-full bg-amber-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400">No source data recorded yet.</p>
                @endforelse
            </div>
        </x-filament::section>

        {{-- Devices --}}
        <x-filament::section>
            <x-slot name="heading">
                Devices (30d)
            </x-slot>

            <div class="space-y-3">
                @forelse ($devices as $device => $count)
                    @php $pct = round(($count / $totalDeviceViews) * 100); @endphp
                    <div>
                        <div class="flex justify-between text-xs font-medium">
                            <span class="capitalize text-gray-700 dark:text-gray-300">{{ $device }}</span>
                            <span class="text-gray-500 dark:text-gray-400">{{ number_format($count) }} ({{ $pct }}%)</span>
                        </div>
                        <div class="mt-1 h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400">No device data recorded yet.</p>
                @endforelse
            </div>
        </x-filament::section>

        {{-- Geography --}}
        <x-filament::section>
            <x-slot name="heading">
                Top Countries (30d)
            </x-slot>

            <div class="space-y-3">
                @forelse ($countries as $country => $count)
                    @php $pct = round(($count / $totalCountryViews) * 100); @endphp
                    <div>
                        <div class="flex justify-between text-xs font-medium">
                            <span class="uppercase font-mono text-gray-700 dark:text-gray-300">{{ $country }}</span>
                            <span class="text-gray-500 dark:text-gray-400">{{ number_format($count) }} ({{ $pct }}%)</span>
                        </div>
                        <div class="mt-1 h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-2 rounded-full bg-blue-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400">No geographic headers detected yet.</p>
                @endforelse
            </div>
        </x-filament::section>
    </div>
</x-filament-widgets::widget>
