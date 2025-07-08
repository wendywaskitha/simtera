<div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 border border-green-200 dark:border-green-800">
    <h3 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-4">✅ Review Hasil Tera</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3">
            <h4 class="font-medium text-gray-900 dark:text-gray-100">Informasi UTTP</h4>
            <div class="bg-white dark:bg-gray-800 rounded p-3 space-y-2 text-sm">
                <div><span class="font-medium">Kode UTTP:</span> {{ $permohonan->uttp->kode_uttp }}</div>
                <div><span class="font-medium">Pemilik:</span> {{ $permohonan->uttp->pemilik->nama }}</div>
                <div><span class="font-medium">Jenis:</span> {{ $permohonan->uttp->jenisUttp->nama }}</div>
                <div><span class="font-medium">Nomor Seri:</span> {{ $permohonan->uttp->nomor_seri }}</div>
            </div>
        </div>

        <div class="space-y-3">
            <h4 class="font-medium text-gray-900 dark:text-gray-100">Hasil Pemeriksaan</h4>
            <div class="bg-white dark:bg-gray-800 rounded p-3 space-y-2 text-sm">
                <div><span class="font-medium">Hasil:</span>
                    <span class="px-2 py-1 rounded text-xs {{ $hasil === 'Sah' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $hasil }}
                    </span>
                </div>
                <div><span class="font-medium">Tanggal Tera:</span> {{ \Carbon\Carbon::parse($tanggal_tera)->format('d M Y') }}</div>
                @if($tanggal_expired)
                <div><span class="font-medium">Tanggal Expired:</span> {{ \Carbon\Carbon::parse($tanggal_expired)->format('d M Y') }}</div>
                @endif
                @if($nomor_sertifikat)
                <div><span class="font-medium">Nomor Sertifikat:</span> {{ $nomor_sertifikat }}</div>
                @endif
                <div><span class="font-medium">Petugas:</span> {{ $petugas_tera }}</div>
            </div>
        </div>
    </div>

    @if($catatan)
    <div class="mt-4">
        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Catatan Hasil</h4>
        <div class="bg-white dark:bg-gray-800 rounded p-3 text-sm">
            {{ $catatan }}
        </div>
    </div>
    @endif

    @if($foto_hasil)
    <div class="mt-4">
        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Dokumentasi</h4>
        <div class="bg-white dark:bg-gray-800 rounded p-3 text-sm">
            📷 {{ is_array($foto_hasil) ? count($foto_hasil) : 1 }} foto hasil pemeriksaan telah diupload
        </div>
    </div>
    @endif

    <div class="mt-4 p-3 bg-green-100 dark:bg-green-900/30 rounded border border-green-200 dark:border-green-700">
        <p class="text-sm text-green-800 dark:text-green-200">
            ✅ Pastikan semua data hasil pemeriksaan sudah benar. Setelah submit, status UTTP akan otomatis terupdate dan {{ $hasil === 'Sah' ? 'sertifikat akan diterbitkan.' : 'permohonan akan selesai.' }}
        </p>
    </div>
</div>
