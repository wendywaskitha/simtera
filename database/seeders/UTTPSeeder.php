<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UTTP;
use App\Models\Pemilik;
use App\Models\JenisUTTP;
use App\Models\Desa;
use App\Models\Pasar;

class UTTPSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil data referensi
        $jenisUttps = JenisUTTP::where('is_active', true)->get();
        $desas = Desa::where('is_active', true)->get();
        $pasars = Pasar::where('is_active', true)->get();

        // Data sample UTTP dengan pemilik yang dinormalisasi
        $uttps = [
            // UTTP di Pasar
            [
                'pemilik' => [
                    'nama' => 'Hj. Siti Aminah',
                    'nik' => '7413010101850001',
                    'telepon' => '081234567001',
                    'alamat' => 'Jl. Pasar Raha No. 15',
                ],
                'jenis_uttp' => 'Timbangan Digital',
                'merk' => 'Camry',
                'tipe' => 'ACS-30',
                'nomor_seri' => 'CAM001',
                'kapasitas_maksimum' => 30.000,
                'daya_baca' => 0.005,
                'tahun_pembuatan' => 2020,
                'desa' => 'Sawerigadi',
                'alamat_lengkap' => 'Pasar Sentral Raha, Kios A-15',
                'lokasi_type' => 'Pasar',
                'detail_lokasi' => 'Kios A-15',
                'pasar' => 'Pasar Sentral Raha',
                'status_tera' => 'Aktif',
                'tanggal_tera_terakhir' => '2024-01-15',
                'tanggal_expired' => '2025-01-15',
                'nomor_sertifikat' => 'CERT-2024-001',
                'petugas_tera' => 'Ir. Muhammad Ridwan',
            ],
            [
                'pemilik' => [
                    'nama' => 'Pak Usman',
                    'nik' => '7413020202800002',
                    'telepon' => '081234567002',
                    'alamat' => 'Jl. Pasar Kusambi No. 8',
                ],
                'jenis_uttp' => 'Timbangan Mekanik',
                'merk' => 'Henher',
                'tipe' => 'TBI-150',
                'nomor_seri' => 'HEN002',
                'kapasitas_maksimum' => 150.000,
                'daya_baca' => 0.050,
                'tahun_pembuatan' => 2019,
                'desa' => 'Kusambi',
                'alamat_lengkap' => 'Pasar Kusambi, Lapak B-8',
                'lokasi_type' => 'Pasar',
                'detail_lokasi' => 'Lapak B-8',
                'pasar' => 'Pasar Kusambi',
                'status_tera' => 'Expired',
                'tanggal_tera_terakhir' => '2023-06-20',
                'tanggal_expired' => '2024-06-20',
                'nomor_sertifikat' => 'CERT-2023-045',
                'petugas_tera' => 'S.T. Andi Kurniawan',
            ],

            // UTTP kedua untuk Hj. Siti Aminah (menunjukkan relasi one-to-many)
            [
                'pemilik' => [
                    'nama' => 'Hj. Siti Aminah', // Pemilik yang sama
                    'nik' => '7413010101850001',
                    'telepon' => '081234567001',
                    'alamat' => 'Jl. Pasar Raha No. 15',
                ],
                'jenis_uttp' => 'Timbangan Digital',
                'merk' => 'Camry',
                'tipe' => 'ACS-15',
                'nomor_seri' => 'CAM006',
                'kapasitas_maksimum' => 15.000,
                'daya_baca' => 0.005,
                'tahun_pembuatan' => 2021,
                'desa' => 'Sawerigadi',
                'alamat_lengkap' => 'Pasar Sentral Raha, Kios A-16',
                'lokasi_type' => 'Pasar',
                'detail_lokasi' => 'Kios A-16',
                'pasar' => 'Pasar Sentral Raha',
                'status_tera' => 'Aktif',
                'tanggal_tera_terakhir' => '2024-02-10',
                'tanggal_expired' => '2025-02-10',
                'nomor_sertifikat' => 'CERT-2024-012',
                'petugas_tera' => 'Ir. Muhammad Ridwan',
            ],

            // UTTP Luar Pasar (SPBU)
            [
                'pemilik' => [
                    'nama' => 'PT. Pertamina Retail',
                    'nik' => null,
                    'telepon' => '081234567003',
                    'alamat' => 'Jl. Raya Raha-Baubau KM 5',
                ],
                'jenis_uttp' => 'Takaran BBM',
                'merk' => 'Tokico',
                'tipe' => 'FRO-0631',
                'nomor_seri' => 'TOK003',
                'kapasitas_maksimum' => null,
                'daya_baca' => 0.001,
                'tahun_pembuatan' => 2021,
                'desa' => 'Sawerigadi',
                'alamat_lengkap' => 'SPBU 44.501.15 Raha',
                'lokasi_type' => 'Luar Pasar',
                'detail_lokasi' => 'SPBU 44.501.15',
                'pasar' => null,
                'status_tera' => 'Aktif',
                'tanggal_tera_terakhir' => '2024-03-10',
                'tanggal_expired' => '2025-03-10',
                'nomor_sertifikat' => 'CERT-2024-025',
                'petugas_tera' => 'Ir. Muhammad Ridwan',
            ],
            [
                'pemilik' => [
                    'nama' => 'CV. Shell Indonesia',
                    'nik' => null,
                    'telepon' => '081234567004',
                    'alamat' => 'Jl. Trans Sulawesi KM 12',
                ],
                'jenis_uttp' => 'Takaran BBM',
                'merk' => 'Wayne',
                'tipe' => 'Helix-6000',
                'nomor_seri' => 'WAY004',
                'kapasitas_maksimum' => null,
                'daya_baca' => 0.001,
                'tahun_pembuatan' => 2022,
                'desa' => 'Lawa',
                'alamat_lengkap' => 'SPBU Shell Lawa',
                'lokasi_type' => 'Luar Pasar',
                'detail_lokasi' => 'SPBU Shell',
                'pasar' => null,
                'status_tera' => 'Belum Tera',
                'tanggal_tera_terakhir' => null,
                'tanggal_expired' => null,
                'nomor_sertifikat' => null,
                'petugas_tera' => null,
            ],

            // UTTP Toko/Usaha
            [
                'pemilik' => [
                    'nama' => 'Toko Makmur Jaya',
                    'nik' => '7413030303750003',
                    'telepon' => '081234567005',
                    'alamat' => 'Jl. Pendidikan No. 25',
                ],
                'jenis_uttp' => 'Timbangan Digital',
                'merk' => 'Acis',
                'tipe' => 'A12E',
                'nomor_seri' => 'ACI005',
                'kapasitas_maksimum' => 6.000,
                'daya_baca' => 0.002,
                'tahun_pembuatan' => 2023,
                'desa' => 'Kusambi',
                'alamat_lengkap' => 'Toko Makmur Jaya, Jl. Pendidikan No. 25',
                'lokasi_type' => 'Luar Pasar',
                'detail_lokasi' => 'Toko Makmur Jaya',
                'pasar' => null,
                'status_tera' => 'Aktif',
                'tanggal_tera_terakhir' => '2024-02-28',
                'tanggal_expired' => '2025-02-28',
                'nomor_sertifikat' => 'CERT-2024-018',
                'petugas_tera' => 'Siti Nurhaliza, S.Kom',
            ],
        ];

        // Array untuk menyimpan pemilik yang sudah dibuat
        $createdPemiliks = [];

        foreach ($uttps as $uttpData) {
            // Cari jenis UTTP
            $jenisUttp = $jenisUttps->where('nama', $uttpData['jenis_uttp'])->first();
            if (!$jenisUttp) {
                $this->command->warn("Jenis UTTP '{$uttpData['jenis_uttp']}' tidak ditemukan. Melewati data ini.");
                continue;
            }

            // Cari desa
            $desa = $desas->where('nama', $uttpData['desa'])->first();
            if (!$desa) {
                $this->command->warn("Desa '{$uttpData['desa']}' tidak ditemukan. Melewati data ini.");
                continue;
            }

            // Cari pasar jika lokasi type adalah Pasar
            $pasar = null;
            if ($uttpData['lokasi_type'] === 'Pasar' && $uttpData['pasar']) {
                $pasar = $pasars->where('nama', $uttpData['pasar'])->first();
                if (!$pasar) {
                    $this->command->warn("Pasar '{$uttpData['pasar']}' tidak ditemukan. Melewati data ini.");
                    continue;
                }
            }

            // Cari atau buat pemilik
            $pemilikData = $uttpData['pemilik'];
            $pemilikKey = $pemilikData['nama'] . '|' . ($pemilikData['nik'] ?? 'no-nik');

            if (!isset($createdPemiliks[$pemilikKey])) {
                // Cek apakah pemilik sudah ada di database
                $existingPemilik = Pemilik::where('nama', $pemilikData['nama'])
                    ->where('nik', $pemilikData['nik'])
                    ->first();

                if ($existingPemilik) {
                    $createdPemiliks[$pemilikKey] = $existingPemilik;
                } else {
                    // Buat pemilik baru
                    $createdPemiliks[$pemilikKey] = Pemilik::create([
                        'nama' => $pemilikData['nama'],
                        'nik' => $pemilikData['nik'],
                        'telepon' => $pemilikData['telepon'],
                        'alamat' => $pemilikData['alamat'],
                        'is_active' => true,
                    ]);

                    $this->command->info("Pemilik '{$pemilikData['nama']}' berhasil dibuat.");
                }
            }

            $pemilik = $createdPemiliks[$pemilikKey];

            // Buat UTTP
            $uttp = UTTP::create([
                'pemilik_id' => $pemilik->id,
                'jenis_uttp_id' => $jenisUttp->id,
                'merk' => $uttpData['merk'],
                'tipe' => $uttpData['tipe'],
                'nomor_seri' => $uttpData['nomor_seri'],
                'kapasitas_maksimum' => $uttpData['kapasitas_maksimum'],
                'daya_baca' => $uttpData['daya_baca'],
                'tahun_pembuatan' => $uttpData['tahun_pembuatan'],
                'desa_id' => $desa->id,
                'alamat_lengkap' => $uttpData['alamat_lengkap'],
                'lokasi_type' => $uttpData['lokasi_type'],
                'detail_lokasi' => $uttpData['detail_lokasi'],
                'status_tera' => $uttpData['status_tera'],
                'tanggal_tera_terakhir' => $uttpData['tanggal_tera_terakhir'],
                'tanggal_expired' => $uttpData['tanggal_expired'],
                'nomor_sertifikat' => $uttpData['nomor_sertifikat'],
                'petugas_tera' => $uttpData['petugas_tera'],
                'is_active' => true,
            ]);

            $this->command->info("UTTP '{$uttp->kode_uttp}' berhasil dibuat untuk pemilik '{$pemilik->nama}'.");
        }

        $this->command->info('Seeder UTTP selesai dijalankan.');
    }
}
