<div class="space-y-6">
    @if($previous)
        @php
            $currentPersentase = $current->persentase_lulus;
            $previousPersentase = $previous->persentase_lulus;
            $trendPersentase = $currentPersentase - $previousPersentase;
            $trendIcon = $trendPersentase > 0 ? '📈' : ($trendPersentase < 0 ? '📉' : '➡️');
            $trendColor = $trendPersentase > 0 ? 'green' : ($trendPersentase < 0 ? 'red' : 'gray');
            
            $teraGrowth = $previous->total_tera_dilakukan > 0 ? 
                round((($current->total_tera_dilakukan - $previous->total_tera_dilakukan) / $previous->total_tera_dilakukan) * 100, 1) : 0;
        @endphp
        
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ $trendIcon }} Perbandingan dengan Bulan Sebelumnya
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Tingkat Keberhasilan -->
                <div class="text-center">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Tingkat Keberhasilan</div>
                    <div class="text-2xl font-bold text-{{ $trendColor }}-600">
                        {{ $currentPersentase }}%
                    </div>
                    <div class="text-sm text-{{ $trendColor }}-600">
                        {{ $trendPersentase > 0 ? '+' : '' }}{{ number_format($trendPersentase, 1) }}% 
                        dari bulan lalu
                    </div>
                </div>
                
                <!-- Total Tera -->
                <div class="text-center">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Tera Dilakukan</div>
                    <div class="text-2xl font-bold text-blue-600">
                        {{ number_format($current->total_tera_dilakukan) }}
                    </div>
                    <div class="text-sm text-{{ $teraGrowth >= 0 ? 'green' : 'red' }}-600">
                        {{ $teraGrowth > 0 ? '+' : '' }}{{ $teraGrowth }}% 
                        dari bulan lalu
                    </div>
                </div>
                
                <!-- Tera Lulus -->
                <div class="text-center">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Tera Lulus</div>
                    <div class="text-2xl font-bold text-green-600">
                        {{ number_format($current->total_tera_lulus) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        vs {{ number_format($previous->total_tera_lulus) }} bulan lalu
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Performance Insights -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800">
            <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">
                💡 Insights & Rekomendasi
            </h4>
            
            <div class="space-y-3">
                @if($trendPersentase > 5)
                    <div class="flex items-start space-x-2">
                        <span class="text-green-500">✅</span>
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Performa Meningkat:</strong> Tingkat keberhasilan tera naik {{ number_format($trendPersentase, 1) }}% dari bulan sebelumnya. Pertahankan kualitas pelayanan ini.
                        </p>
                    </div>
                @elseif($trendPersentase < -5)
                    <div class="flex items-start space-x-2">
                        <span class="text-red-500">⚠️</span>
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Perlu Perhatian:</strong> Tingkat keberhasilan turun {{ number_format(abs($trendPersentase), 1) }}%. Evaluasi proses tera dan pelatihan petugas.
                        </p>
                    </div>
                @else
                    <div class="flex items-start space-x-2">
                        <span class="text-blue-500">ℹ️</span>
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Performa Stabil:</strong> Tingkat keberhasilan relatif stabil. Fokus pada peningkatan efisiensi proses.
                        </p>
                    </div>
                @endif
                
                @if($teraGrowth > 10)
                    <div class="flex items-start space-x-2">
                        <span class="text-green-500">📈</span>
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Volume Meningkat:</strong> Jumlah tera naik {{ $teraGrowth }}%. Pastikan kapasitas petugas mencukupi.
                        </p>
                    </div>
                @elseif($teraGrowth < -10)
                    <div class="flex items-start space-x-2">
                        <span class="text-yellow-500">📉</span>
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Volume Menurun:</strong> Jumlah tera turun {{ abs($teraGrowth) }}%. Evaluasi penyebab dan tingkatkan sosialisasi.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700 text-center">
            <div class="text-4xl mb-2">📊</div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                Data Perbandingan Tidak Tersedia
            </h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Belum ada data laporan bulan sebelumnya untuk perbandingan trend.
            </p>
        </div>
    @endif
</div>
