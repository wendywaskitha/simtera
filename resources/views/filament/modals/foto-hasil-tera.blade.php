<div class="p-6">
    @if(!empty($record->foto_hasil))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($record->foto_hasil as $index => $foto)
                <div class="relative group">
                    <img src="{{ Storage::url($foto) }}" 
                         alt="Foto Hasil Tera {{ $index + 1 }}" 
                         class="w-full h-48 object-cover rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-lg flex items-center justify-center">
                        <a href="{{ Storage::url($foto) }}" 
                           target="_blank" 
                           class="opacity-0 group-hover:opacity-100 bg-white text-gray-800 px-3 py-1 rounded text-sm font-medium transition-opacity">
                            Lihat Penuh
                        </a>
                    </div>
                    <div class="absolute bottom-2 left-2 bg-black bg-opacity-70 text-white px-2 py-1 rounded text-xs">
                        Foto {{ $index + 1 }}
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            <div class="text-4xl mb-2">📷</div>
            <p>Tidak ada foto hasil tera yang tersedia</p>
        </div>
    @endif
</div>
