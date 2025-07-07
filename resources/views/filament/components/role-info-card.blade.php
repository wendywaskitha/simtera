<div class="bg-{{ $info['color'] === 'danger' ? 'red' : ($info['color'] === 'warning' ? 'yellow' : ($info['color'] === 'success' ? 'green' : ($info['color'] === 'info' ? 'blue' : 'gray'))) }}-50 dark:bg-{{ $info['color'] === 'danger' ? 'red' : ($info['color'] === 'warning' ? 'yellow' : ($info['color'] === 'success' ? 'green' : ($info['color'] === 'info' ? 'blue' : 'gray'))) }}-900/20 rounded-lg p-4 border border-{{ $info['color'] === 'danger' ? 'red' : ($info['color'] === 'warning' ? 'yellow' : ($info['color'] === 'success' ? 'green' : ($info['color'] === 'info' ? 'blue' : 'gray'))) }}-200 dark:border-{{ $info['color'] === 'danger' ? 'red' : ($info['color'] === 'warning' ? 'yellow' : ($info['color'] === 'success' ? 'green' : ($info['color'] === 'info' ? 'blue' : 'gray'))) }}-800">
    <div class="flex items-start space-x-3">
        <div class="text-2xl">
            @switch($info['color'])
                @case('danger')
                    🛡️
                    @break
                @case('warning')
                    ⭐
                    @break
                @case('success')
                    🔧
                    @break
                @case('info')
                    📋
                    @break
                @default
                    👤
            @endswitch
        </div>
        <div class="flex-1">
            <h4 class="font-semibold text-{{ $info['color'] === 'danger' ? 'red' : ($info['color'] === 'warning' ? 'yellow' : ($info['color'] === 'success' ? 'green' : ($info['color'] === 'info' ? 'blue' : 'gray'))) }}-900 dark:text-{{ $info['color'] === 'danger' ? 'red' : ($info['color'] === 'warning' ? 'yellow' : ($info['color'] === 'success' ? 'green' : ($info['color'] === 'info' ? 'blue' : 'gray'))) }}-100 mb-1">
                {{ $info['title'] }}
            </h4>
            <p class="text-sm text-{{ $info['color'] === 'danger' ? 'red' : ($info['color'] === 'warning' ? 'yellow' : ($info['color'] === 'success' ? 'green' : ($info['color'] === 'info' ? 'blue' : 'gray'))) }}-700 dark:text-{{ $info['color'] === 'danger' ? 'red' : ($info['color'] === 'warning' ? 'yellow' : ($info['color'] === 'success' ? 'green' : ($info['color'] === 'info' ? 'blue' : 'gray'))) }}-300 mb-2">
                {{ $info['description'] }}
            </p>
            <div class="text-xs text-{{ $info['color'] === 'danger' ? 'red' : ($info['color'] === 'warning' ? 'yellow' : ($info['color'] === 'success' ? 'green' : ($info['color'] === 'info' ? 'blue' : 'gray'))) }}-600 dark:text-{{ $info['color'] === 'danger' ? 'red' : ($info['color'] === 'warning' ? 'yellow' : ($info['color'] === 'success' ? 'green' : ($info['color'] === 'info' ? 'blue' : 'gray'))) }}-400">
                <strong>Permissions:</strong> {{ $info['permissions'] }}
            </div>
        </div>
    </div>
</div>
