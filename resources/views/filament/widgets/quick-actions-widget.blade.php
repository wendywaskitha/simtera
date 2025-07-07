<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            🚀 Quick Actions
        </x-slot>
        
        <x-slot name="description">
            Akses cepat ke fitur-fitur utama sistem
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($actions as $action)
                <a href="{{ $action['url'] }}" 
                   class="block p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-{{ $action['color'] }}-300 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-{{ $action['color'] }}-100 dark:bg-{{ $action['color'] }}-900/20 rounded-lg flex items-center justify-center text-xl group-hover:scale-110 transition-transform duration-200">
                                {{ $action['icon'] }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-{{ $action['color'] }}-600 transition-colors duration-200">
                                {{ $action['label'] }}
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $action['description'] }}
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-{{ $action['color'] }}-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
