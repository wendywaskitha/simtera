<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PermohonanTera;
use App\Models\HasilTera;
use App\Models\UTTP;
use Carbon\Carbon;

class PermohonanTeraSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Menghapus data terkait...');

        // Hapus data hasil tera terlebih dahulu
        HasilTera::query()->delete();

        // Kemudian hapus permohonan tera
        PermohonanTera::query()->delete();

        $this->command->info('Membuat data permohonan tera...');

        $this->createPermohonanTeraData();
    }

    private function createPermohonanTeraData()
    {
        $uttps = UTTP::aktif()->take(50)->get();

        if ($uttps->isEmpty()) {
            $this->command->warn('Tidak ada data UTTP. Jalankan UTTPSeeder terlebih dahulu.');
            return;
        }

        $jenisLayanan = ['Di Kantor', 'Luar Kantor', 'Sidang Tera'];
        $statuses = ['Pending', 'Disetujui', 'Dijadwalkan', 'Selesai'];
        $petugas = [
            'Ir. Muhammad Ridwan',
            'S.T. Andi Kurniawan',
            'Siti Nurhaliza, S.Kom',
            'Muh. Fadli Rahman, A.Md'
        ];

        foreach ($uttps as $index => $uttp) {
            $jenisLayananSelected = $uttp->lokasi_type === 'Pasar' ? 'Sidang Tera' :
                                   ($uttp->lokasi_type === 'Luar Pasar' ? 'Luar Kantor' :
                                   $jenisLayanan[array_rand($jenisLayanan)]);

            $status = $statuses[array_rand($statuses)];
            $tanggalPermohonan = Carbon::now()->subDays(rand(1, 60));

            PermohonanTera::create([
                'nomor_permohonan' => 'TRA' . date('Ym') . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'uttp_id' => $uttp->id,
                'jenis_layanan' => $jenisLayananSelected,
                'status' => $status,
                'tanggal_permohonan' => $tanggalPermohonan,
                'tanggal_jadwal' => in_array($status, ['Dijadwalkan', 'Selesai']) ?
                                   $tanggalPermohonan->copy()->addDays(rand(1, 14)) : null,
                'catatan_pemohon' => $this->generateCatatanPemohon($uttp, $jenisLayananSelected),
                'catatan_petugas' => in_array($status, ['Disetujui', 'Dijadwalkan', 'Selesai']) ?
                                    'Permohonan telah diproses sesuai prosedur' : null,
                'dokumen_pendukung' => $jenisLayananSelected === 'Luar Kantor' ?
                                      ['surat_permohonan.pdf'] : null,
                'petugas_assigned' => in_array($status, ['Dijadwalkan', 'Selesai']) ?
                                     $petugas[array_rand($petugas)] : null,
                'created_at' => $tanggalPermohonan,
                'updated_at' => $tanggalPermohonan->copy()->addHours(rand(1, 48)),
            ]);
        }

        $this->command->info('Berhasil membuat ' . $uttps->count() . ' data permohonan tera.');
    }

    private function generateCatatanPemohon($uttp, $jenisLayanan)
    {
        $catatan = [
            'Di Kantor' => [
                'Mohon dijadwalkan tera untuk ' . $uttp->jenisUttp->nama,
                'Permohonan tera rutin untuk ' . $uttp->jenisUttp->nama,
                'Tera ulang untuk ' . $uttp->jenisUttp->nama . ' milik ' . $uttp->pemilik->nama,
            ],
            'Luar Kantor' => [
                'Mohon kunjungan petugas untuk tera di lokasi',
                'Permohonan tera di tempat untuk ' . $uttp->jenisUttp->nama,
                'UTTP tidak dapat dibawa ke kantor, mohon tera di lokasi',
            ],
            'Sidang Tera' => [
                'Ikut serta dalam sidang tera di ' . ($uttp->pasar->nama ?? 'pasar'),
                'Permohonan tera dalam sidang tera massal',
                'Pendaftaran untuk sidang tera di pasar',
            ]
        ];

        return $catatan[$jenisLayanan][array_rand($catatan[$jenisLayanan])];
    }
}
