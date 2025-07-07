<div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800">
    @if($isExisting ?? false)
        <div class="flex items-center mb-4">
            <div class="text-2xl mr-3">📊</div>
            <div>
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Laporan Sudah Ada</h3>
                <p class="text-sm text-blue-700 dark:text-blue-300">{{ $laporan->periode_lengkap }}</p>
            </div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ number_format($laporan->total_uttp_terdaftar) }}</div>
                <div class="text-xs text-gray-600 dark:text-gray-400">Total UTTP</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ number_format($laporan->total_tera_dilakukan) }}</div>
                <div class="text-xs text-gray-600 dark:text-gray-400">Tera Dilakukan</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-green-600">{{ number_format($laporan->total_tera_lulus) }}</div>
                <div class="text-xs text-gray-600 dark:text-gray-400">Tera Lulus</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $laporan->persentase_lulus }}%</div>
                <div class="text-xs text-gray-600 dark:text-gray-400">Tingkat Lulus</div>
            </div>
        </div>
        
        <div class="mt-4 p-3 bg-blue-100 dark:bg-blue-900/30 rounded border border-blue-200 dark:border-blue-700">
            <p class="text-sm text-blue-800 dark:text-blue-200">
                ✅ Laporan untuk periode ini sudah tersedia. Anda dapat memilih "Generate Ulang" untuk memperbarui data.
            </p>
        </div>
    @else
        <div class="flex items-center mb-4">
            <div class="text-2xl mr-3">📈</div>
            <div>
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Preview Laporan Baru</h3>
                <p class="text-sm text-blue-700 dark:text-blue-300">{{ $periode ?? 'Periode belum dipilih' }}</p>
            </div>
        </div>
        
        @if(isset($total_uttp))
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ number_format($total_uttp) }}</div>
                <div class="text-xs text-gray-600 dark:text-gray-400">Total UTTP</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ number_format($total_tera) }}</div>
                <div class="text-xs text-gray-600 dark:text-gray-400">Tera Dilakukan</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-green-600">{{ number_format($total_lulus) }}</div>
                <div class="text-xs text-gray-600 dark:text-gray-400">Tera Lulus</div>
            </div>
        </div>
        @endif
        
        <div class="mt-4 p-3 bg-green-100 dark:bg-green-900/30 rounded border border-green-200 dark:border-green-700">
            <p class="text-sm text-green-800 dark:text-green-200">
                🆕 Laporan baru akan dibuat dengan data terbaru dari sistem.
            </p>
        </div>
    @endif
</div>
