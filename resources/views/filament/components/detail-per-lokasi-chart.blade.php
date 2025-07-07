<div class="space-y-4">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        @foreach($data as $item)
            @php
                $persentase = $item['total'] > 0 ? round(($item['lulus'] / $item['total']) * 100, 1) : 0;
                $colorClass = $persentase >= 90 ? 'green' : ($persentase >= 75 ? 'yellow' : 'red');
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm mb-2">
                        {{ $item['nama'] ?? $item['kecamatan'] }}
                    </h4>
                    
                    <div class="text-2xl font-bold mb-1 
                        {{ $colorClass === 'green' ? 'text-green-600' : 
                           ($colorClass === 'yellow' ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $persentase }}%
                    </div>
                    
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $item['lulus'] }}/{{ $item['total'] }} lulus
                    </div>
                    
                    <!-- Mini Progress Bar -->
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-1">
                            <div class="h-1 rounded-full {{ $colorClass === 'green' ? 'bg-green-500' : 
                                                            ($colorClass === 'yellow' ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                 style="width: {{ $persentase }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Detailed Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Detail Per Kecamatan</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-900 dark:text-gray-100">Kecamatan</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-900 dark:text-gray-100">Total Tera</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-900 dark:text-gray-100">Lulus</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-900 dark:text-gray-100">Tidak Lulus</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-900 dark:text-gray-100">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($data as $item)
                        @php
                            $persentase = $item['total'] > 0 ? round(($item['lulus'] / $item['total']) * 100, 1) : 0;
                            $tidakLulus = $item['total'] - $item['lulus'];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-gray-100">
                                {{ $item['nama'] ?? $item['kecamatan'] }}
                            </td>
                            <td class="px-4 py-2 text-center text-gray-600 dark:text-gray-400">
                                {{ number_format($item['total']) }}
                            </td>
                            <td class="px-4 py-2 text-center text-green-600 font-medium">
                                {{ number_format($item['lulus']) }}
                            </td>
                            <td class="px-4 py-2 text-center text-red-600 font-medium">
                                {{ number_format($tidakLulus) }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="px-2 py-1 rounded text-xs font-medium 
                                    {{ $persentase >= 90 ? 'bg-green-100 text-green-800' : 
                                       ($persentase >= 75 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $persentase }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
