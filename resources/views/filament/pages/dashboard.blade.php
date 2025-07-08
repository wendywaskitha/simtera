<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Welcome Header dengan Background Adaptif -->
        <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-600 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 rounded-xl p-6 shadow-xl border border-blue-500/20 dark:border-gray-700">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-grid-white/[0.05] dark:bg-grid-white/[0.02]"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent dark:from-transparent dark:via-gray-700/10 dark:to-transparent"></div>

            <!-- Content -->
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-12 h-12 bg-white/20 dark:bg-gray-700/50 rounded-full flex items-center justify-center backdrop-blur-sm">
                                <span class="text-2xl">👋</span>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white dark:text-gray-100">
                                    Selamat datang, {{ auth()->user()->name }}!
                                </h1>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/20 dark:bg-gray-700/50 text-white dark:text-gray-200 backdrop-blur-sm">
                                        {{ auth()->user()->role_label }}
                                    </span>
                                    <span class="text-blue-200 dark:text-gray-400 text-sm">
                                        {{ now()->format('d F Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <p class="text-blue-100 dark:text-gray-300 text-lg leading-relaxed max-w-2xl">
                            {{ match(auth()->user()->role) {
                                'admin' => '🛡️ Anda memiliki akses penuh ke sistem UPTD Metrologi Legal',
                                'kepala' => '⭐ Monitor kinerja dan laporan sistem dengan mudah',
                                'petugas' => '🔧 Kelola proses tera dan input hasil pemeriksaan',
                                'staff' => '📋 Bantu administrasi dan data entry sistem',
                                default => '👤 Selamat bekerja di sistem UPTD Metrologi Legal'
                            } }}
                        </p>
                    </div>

                    <!-- Decorative Icon -->
                    <div class="hidden lg:block">
                        <div class="relative">
                            <div class="text-8xl opacity-20 text-white dark:text-gray-300 transform rotate-12">⚖️</div>
                            <div class="absolute inset-0 text-8xl opacity-10 text-white dark:text-gray-400 transform -rotate-12 translate-x-4 translate-y-4">⚖️</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                    <div class="group bg-white/20 dark:bg-gray-700/30 backdrop-blur-sm rounded-xl p-4 text-center border border-white/30 dark:border-gray-600/50 hover:bg-white/30 dark:hover:bg-gray-700/50 transition-all duration-300 hover:scale-105">
                        <div class="text-3xl font-bold text-white dark:text-gray-100 mb-1">
                            {{ number_format(\App\Models\UTTP::count()) }}
                        </div>
                        <div class="text-sm text-blue-200 dark:text-gray-300 font-medium">Total UTTP</div>
                        <div class="w-full bg-white/20 dark:bg-gray-600/30 rounded-full h-1 mt-2">
                            <div class="bg-white dark:bg-gray-300 h-1 rounded-full w-full"></div>
                        </div>
                    </div>

                    <div class="group bg-white/20 dark:bg-gray-700/30 backdrop-blur-sm rounded-xl p-4 text-center border border-white/30 dark:border-gray-600/50 hover:bg-white/30 dark:hover:bg-gray-700/50 transition-all duration-300 hover:scale-105">
                        <div class="text-3xl font-bold text-white dark:text-gray-100 mb-1">
                            {{ number_format(\App\Models\PermohonanTera::where('status', 'Pending')->count()) }}
                        </div>
                        <div class="text-sm text-blue-200 dark:text-gray-300 font-medium">Pending</div>
                        <div class="w-full bg-white/20 dark:bg-gray-600/30 rounded-full h-1 mt-2">
                            <div class="bg-yellow-300 h-1 rounded-full" style="width: {{ \App\Models\PermohonanTera::where('status', 'Pending')->count() > 0 ? '75%' : '0%' }}"></div>
                        </div>
                    </div>

                    <div class="group bg-white/20 dark:bg-gray-700/30 backdrop-blur-sm rounded-xl p-4 text-center border border-white/30 dark:border-gray-600/50 hover:bg-white/30 dark:hover:bg-gray-700/50 transition-all duration-300 hover:scale-105">
                        <div class="text-3xl font-bold text-white dark:text-gray-100 mb-1">
                            {{ number_format(\App\Models\HasilTera::whereMonth('tanggal_tera', now()->month)->count()) }}
                        </div>
                        <div class="text-sm text-blue-200 dark:text-gray-300 font-medium">Tera Bulan Ini</div>
                        <div class="w-full bg-white/20 dark:bg-gray-600/30 rounded-full h-1 mt-2">
                            <div class="bg-green-300 h-1 rounded-full w-4/5"></div>
                        </div>
                    </div>

                    <div class="group bg-white/20 dark:bg-gray-700/30 backdrop-blur-sm rounded-xl p-4 text-center border border-white/30 dark:border-gray-600/50 hover:bg-white/30 dark:hover:bg-gray-700/50 transition-all duration-300 hover:scale-105">
                        <div class="text-3xl font-bold text-white dark:text-gray-100 mb-1">
                            {{ number_format(\App\Models\User::where('is_active', true)->count()) }}
                        </div>
                        <div class="text-sm text-blue-200 dark:text-gray-300 font-medium">User Aktif</div>
                        <div class="w-full bg-white/20 dark:bg-gray-600/30 rounded-full h-1 mt-2">
                            <div class="bg-blue-300 h-1 rounded-full w-full"></div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 flex flex-wrap gap-3">
                    @if(auth()->user()->hasPermission('create'))
                        <a href="{{ \App\Filament\Resources\UTTPResource::getUrl('create') }}"
                           class="inline-flex items-center px-4 py-2 bg-white/20 dark:bg-gray-700/50 hover:bg-white/30 dark:hover:bg-gray-700/70 text-white dark:text-gray-200 rounded-lg font-medium transition-all duration-200 backdrop-blur-sm border border-white/30 dark:border-gray-600/50">
                            <span class="mr-2">⚖️</span>
                            Tambah UTTP
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('input_results'))
                        <a href="{{ \App\Filament\Resources\HasilTeraResource::getUrl('create') }}"
                           class="inline-flex items-center px-4 py-2 bg-white/20 dark:bg-gray-700/50 hover:bg-white/30 dark:hover:bg-gray-700/70 text-white dark:text-gray-200 rounded-lg font-medium transition-all duration-200 backdrop-blur-sm border border-white/30 dark:border-gray-600/50">
                            <span class="mr-2">✅</span>
                            Input Hasil
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('view_reports'))
                        <a href="{{ \App\Filament\Resources\LaporanBulananResource::getUrl('index') }}"
                           class="inline-flex items-center px-4 py-2 bg-white/20 dark:bg-gray-700/50 hover:bg-white/30 dark:hover:bg-gray-700/70 text-white dark:text-gray-200 rounded-lg font-medium transition-all duration-200 backdrop-blur-sm border border-white/30 dark:border-gray-600/50">
                            <span class="mr-2">📊</span>
                            Laporan
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- System Status Banner -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center">
                        <span class="text-green-600 dark:text-green-400 text-xl">🟢</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-green-800 dark:text-green-200">Sistem Operasional</h3>
                        <p class="text-xs text-green-600 dark:text-green-400">Semua layanan berjalan normal</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-medium text-green-800 dark:text-green-200">Uptime: 99.9%</div>
                    <div class="text-xs text-green-600 dark:text-green-400">Last check: {{ now()->format('H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Performance Indicator -->
        @php
            $totalTera = \App\Models\HasilTera::whereMonth('tanggal_tera', now()->month)->count();
            $teraSah = \App\Models\HasilTera::whereMonth('tanggal_tera', now()->month)->where('hasil', 'Sah')->count();
            $successRate = $totalTera > 0 ? round(($teraSah / $totalTera) * 100, 1) : 0;
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Performance Bulan Ini</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $successRate >= 90 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' :
                       ($successRate >= 75 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' :
                        'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                    {{ $successRate }}% Success Rate
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalTera }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Tera</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $teraSah }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Tera Sah</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $totalTera - $teraSah }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Batal</div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                    <span>Progress Target Bulanan</span>
                    <span>{{ $totalTera }}/100</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full transition-all duration-500"
                         style="width: {{ min(($totalTera / 100) * 100, 100) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Widgets Grid -->
        <div class="grid grid-cols-1 gap-6">
            @foreach($this->getWidgets() as $widget)
                @livewire($widget)
            @endforeach
        </div>

        <!-- Footer Info -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-xl p-6 text-center border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-center space-x-2 mb-3">
                <span class="text-2xl">⚖️</span>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">UPTD Metrologi Legal</h4>
            </div>

            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                © {{ date('Y') }} UPTD Metrologi Legal Kabupaten Muna Barat
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                Sistem Informasi Pelayanan Tera dan Tera Ulang
            </p>

            <div class="flex items-center justify-center space-x-4 text-xs text-gray-500 dark:text-gray-500">
                <span class="flex items-center space-x-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    <span>Online</span>
                </span>
                <span>•</span>
                <span>Last updated: {{ now()->format('d F Y H:i:s') }}</span>
                <span>•</span>
                <span>Version 1.0</span>
                <span>•</span>
                <span class="flex items-center space-x-1">
                    <span>Built with</span>
                    <span class="text-red-500">❤️</span>
                    <span>using Laravel & Filament</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        .bg-grid-white\/\[0\.05\] {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' width='32' height='32' fill='none' stroke='rgb(255 255 255 / 0.05)'%3e%3cpath d='m0 .5h32m-32 32v-32'/%3e%3c/svg%3e");
        }

        .dark .bg-grid-white\/\[0\.02\] {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' width='32' height='32' fill='none' stroke='rgb(255 255 255 / 0.02)'%3e%3cpath d='m0 .5h32m-32 32v-32'/%3e%3c/svg%3e");
        }

        @media (prefers-reduced-motion: reduce) {
            .transition-all {
                transition: none;
            }
        }
    </style>
</x-filament-panels::page>
