<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($data as $item)
            @php
                $persentase = $item['total'] > 0 ? round(($item['Sah'] / $item['total']) * 100, 1) : 0;
                $colorClass = $persentase >= 90 ? 'green' : ($persentase >= 75 ? 'yellow' : 'red');
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ $item['nama'] ?? $item['jenis'] }}</h4>
                    <span class="px-2 py-1 rounded text-xs font-medium
                        {{ $colorClass === 'green' ? 'bg-green-100 text-green-800' :
                           ($colorClass === 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ $persentase }}%
                    </span>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Total:</span>
                        <span class="font-medium">{{ number_format($item['total']) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Sah:</span>
                        <span class="font-medium text-green-600">{{ number_format($item['sah']) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Batal:</span>
                        <span class="font-medium text-red-600">{{ number_format($item['total'] - $item['sah']) }}</span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $colorClass === 'green' ? 'bg-green-500' :
                                                        ($colorClass === 'yellow' ? 'bg-yellow-500' : 'bg-red-500') }}"
                             style="width: {{ $persentase }}%"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
