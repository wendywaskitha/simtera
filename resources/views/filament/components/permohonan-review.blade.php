<div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800">
    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">📋 Review Permohonan Tera</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3">
            <h4 class="font-medium text-gray-900 dark:text-gray-100">Informasi UTTP</h4>
            <div class="bg-white dark:bg-gray-800 rounded p-3 space-y-2 text-sm">
                <div><span class="font-medium">Kode UTTP:</span> {{ $uttp->kode_uttp }}</div>
                <div><span class="font-medium">Pemilik:</span> {{ $uttp->pemilik->nama }}</div>
                <div><span class="font-medium">Jenis:</span> {{ $uttp->jenisUttp->nama }}</div>
                <div><span class="font-medium">Lokasi:</span> {{ $uttp->lokasi_lengkap }}</div>
            </div>
        </div>

        <div class="space-y-3">
            <h4 class="font-medium text-gray-900 dark:text-gray-100">Detail Permohonan</h4>
            <div class="bg-white dark:bg-gray-800 rounded p-3 space-y-2 text-sm">
                <div><span class="font-medium">Jenis Layanan:</span> {{ $jenis_layanan }}</div>
                <div><span class="font-medium">Tanggal Permohonan:</span> {{ \Carbon\Carbon::parse($tanggal_permohonan)->format('d M Y') }}</div>
                @if($tanggal_jadwal)
                <div><span class="font-medium">Jadwal Diinginkan:</span> {{ \Carbon\Carbon::parse($tanggal_jadwal)->format('d M Y') }}</div>
                @endif
                @if($dokumen)
                <div><span class="font-medium">Dokumen:</span> ✅ Sudah diupload</div>
                @endif
            </div>
        </div>
    </div>

    @if($catatan)
    <div class="mt-4">
        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Catatan Pemohon</h4>
        <div class="bg-white dark:bg-gray-800 rounded p-3 text-sm">
            {{ $catatan }}
        </div>
    </div>
    @endif

    <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded border border-yellow-200 dark:border-yellow-800">
        <p class="text-sm text-yellow-800 dark:text-yellow-200">
            ⚠️ Pastikan semua data sudah benar sebelum submit. Permohonan yang sudah disubmit tidak dapat diubah kecuali oleh admin.
        </p>
    </div>
</div>
