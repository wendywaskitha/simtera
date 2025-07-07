<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @foreach($stats as $label => $value)
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 text-center">
            <div class="text-2xl font-bold text-blue-600 mb-1">{{ number_format($value) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $label }}</div>
        </div>
    @endforeach
</div>
