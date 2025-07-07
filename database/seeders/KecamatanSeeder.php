<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;

class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatans = [
            [
                'nama' => 'Barangka',
                'kode' => '74.13.02',
                'keterangan' => 'Kecamatan Barangka dengan 8 desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Kusambi',
                'kode' => '74.13.10',
                'keterangan' => 'Kecamatan Kusambi dengan 9 desa dan 1 kelurahan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Lawa',
                'kode' => '74.13.03',
                'keterangan' => 'Kecamatan Lawa dengan 6 desa dan 2 kelurahan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Maginti',
                'kode' => '74.13.06',
                'keterangan' => 'Kecamatan Maginti dengan 8 desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Napano Kusambi',
                'kode' => '74.13.11',
                'keterangan' => 'Kecamatan Napano Kusambi dengan 6 desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Sawerigadi',
                'kode' => '74.13.01',
                'keterangan' => 'Kecamatan Sawerigadi dengan 10 desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Tiworo Kepulauan',
                'kode' => '74.13.09',
                'keterangan' => 'Kecamatan Tiworo Kepulauan dengan 7 desa dan 2 kelurahan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Tiworo Selatan',
                'kode' => '74.13.05',
                'keterangan' => 'Kecamatan Tiworo Selatan dengan 5 desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Tiworo Tengah',
                'kode' => '74.13.07',
                'keterangan' => 'Kecamatan Tiworo Tengah dengan 8 desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Tiworo Utara',
                'kode' => '74.13.08',
                'keterangan' => 'Kecamatan Tiworo Utara dengan 7 desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Wadaga',
                'kode' => '74.13.04',
                'keterangan' => 'Kecamatan Wadaga dengan 7 desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($kecamatans as $kecamatan) {
            Kecamatan::create($kecamatan);
        }
    }
}
