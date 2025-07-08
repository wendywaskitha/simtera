<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            📊 Performance Metrics - {{ now()->format('F Y') }}
        </x-slot>

        <x-slot name="description">
            Key Performance Indicators dan metrics kinerja sistem
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Tingkat Keberhasilan -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">Tingkat Keberhasilan</p>
                        <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $tingkat_keberhasilan }}%</p>
                    </div>
                    <div class="text-3xl">🎯</div>
                </div>
                <div class="mt-2">
                    <div class="w-full bg-green-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $tingkat_keberhasilan }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Response Time -->
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Avg Response Time</p>
                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $avg_response_time }} hari</p>
                    </div>
                    <div class="text-3xl">⚡</div>
                </div>
                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Rata-rata waktu proses</p>
            </div>

            <!-- Target Achievement -->
            <div class="bg-gradient-to-r from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Target Achievement</p>
                        <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $achievement }}%</p>
                    </div>
                    <div class="text-3xl">🏆</div>
                </div>
                <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">{{ $total_tera }}/{{ $monthly_target }} target</p>
            </div>

            <!-- Quality Score -->
            <div class="bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-lg p-4 border border-orange-200 dark:border-orange-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-orange-600 dark:text-orange-400">Quality Score</p>
                        <p class="text-2xl font-bold text-orange-900 dark:text-orange-100">
                            @if($tingkat_keberhasilan >= 95)
                                A+
                            @elseif($tingkat_keberhasilan >= 90)
                                A
                            @elseif($tingkat_keberhasilan >= 85)
                                B+
                            @elseif($tingkat_keberhasilan >= 80)
                                B
                            @else
                                C
                            @endif
                        </p>
                    </div>
                    <div class="text-3xl">⭐</div>
                </div>
                <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">Berdasarkan tingkat sah</p>
            </div>
        </div>

        <!-- Top Performing Petugas -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                    <span class="mr-2">👥</span>
                    Top Performing Petugas Bulan Ini
                </h4>
            </div>
            <div class="p-4">
                @if($petugas_performance->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($petugas_performance as $index => $petugas)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                            {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' :
                                               ($index === 1 ? 'bg-gray-100 text-gray-800' :
                                               ($index === 2 ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800')) }}">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $petugas->nama }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $petugas->jabatan }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $petugas->total_tera }} tera</p>
                                    <p class="text-sm text-green-600 dark:text-green-400">{{ $petugas->success_rate }}% sah</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <div class="text-4xl mb-2">📊</div>
                        <p>Belum ada data performance bulan ini</p>
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
