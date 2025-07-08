<div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Informasi UTTP</h4>
            <div class="space-y-1 text-sm">
                <div><span class="font-medium">Kode:</span> {{ $uttp->kode_uttp }}</div>
                <div><span class="font-medium">Pemilik:</span> {{ $uttp->pemilik->nama }}</div>
                <div><span class="font-medium">Jenis:</span> {{ $uttp->jenisUttp->nama }}</div>
                <div><span class="font-medium">Nomor Seri:</span> {{ $uttp->nomor_seri }}</div>
            </div>
        </div>
        <div>
            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Status & Lokasi</h4>
            <div class="space-y-1 text-sm">
                <div><span class="font-medium">Status Tera:</span>
                    <span class="px-2 py-1 rounded text-xs {{ $uttp->status_tera === 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $uttp->status_tera }}
                    </span>
                </div>
                <div><span class="font-medium">Tipe Lokasi:</span> {{ $uttp->lokasi_type }}</div>
                <div><span class="font-medium">Lokasi:</span> {{ $uttp->lokasi_lengkap }}</div>
            </div>
        </div>
    </div>
</div>
