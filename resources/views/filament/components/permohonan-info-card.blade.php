<div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">📋 Informasi Permohonan</h4>
            <div class="space-y-1 text-sm">
                <div><span class="font-medium">Nomor:</span> {{ $permohonan->nomor_permohonan }}</div>
                <div><span class="font-medium">Jenis Layanan:</span> {{ $permohonan->jenis_layanan }}</div>
                <div><span class="font-medium">Status:</span> 
                    <span class="px-2 py-1 rounded text-xs {{ $permohonan->status === 'Dijadwalkan' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $permohonan->status }}
                    </span>
                </div>
                <div><span class="font-medium">Petugas:</span> {{ $permohonan->petugas_assigned ?? 'Belum ditugaskan' }}</div>
            </div>
        </div>
        <div>
            <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">⚖️ Informasi UTTP</h4>
            <div class="space-y-1 text-sm">
                <div><span class="font-medium">Kode:</span> {{ $permohonan->uttp->kode_uttp }}</div>
                <div><span class="font-medium">Pemilik:</span> {{ $permohonan->uttp->nama_pemilik }}</div>
                <div><span class="font-medium">Jenis:</span> {{ $permohonan->uttp->jenisUttp->nama }}</div>
                <div><span class="font-medium">Lokasi:</span> {{ $permohonan->uttp->lokasi_lengkap }}</div>
            </div>
        </div>
    </div>
</div>
