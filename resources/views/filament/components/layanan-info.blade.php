<div class="rounded-lg border p-4 {{ $info['color'] === 'info' ? 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800' : 
    ($info['color'] === 'warning' ? 'bg-yellow-50 border-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-800' : 
    ($info['color'] === 'success' ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800' : 
    'bg-gray-50 border-gray-200 dark:bg-gray-900/20 dark:border-gray-800')) }}">
    <div class="flex items-start space-x-3">
        <div class="text-2xl">{{ $info['icon'] }}</div>
        <div class="flex-1">
            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">{{ $info['title'] }}</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $info['desc'] }}</p>
        </div>
    </div>
</div>
