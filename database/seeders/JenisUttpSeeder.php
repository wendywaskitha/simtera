<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisUTTP;

class JenisUttpSeeder extends Seeder
{
    public function run(): void
    {
        $jenisUttp = [
            [
                'nama' => 'Timbangan Digital',
                'kode' => 'TD',
                'deskripsi' => 'Timbangan elektronik digital untuk keperluan komersial',
                'satuan' => 'kg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Timbangan Mekanik',
                'kode' => 'TM',
                'deskripsi' => 'Timbangan mekanik/analog untuk keperluan komersial',
                'satuan' => 'kg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Takaran BBM',
                'kode' => 'TBBM',
                'deskripsi' => 'Alat takaran bahan bakar minyak di SPBU',
                'satuan' => 'liter',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Takaran LPG',
                'kode' => 'TLPG',
                'deskripsi' => 'Alat takaran gas LPG',
                'satuan' => 'kg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Meter Kain',
                'kode' => 'MK',
                'deskripsi' => 'Alat ukur panjang untuk kain dan tekstil',
                'satuan' => 'meter',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Anak Timbangan',
                'kode' => 'AT',
                'deskripsi' => 'Anak timbangan standar untuk kalibrasi',
                'satuan' => 'kg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Timbangan Gantung',
                'kode' => 'TG',
                'deskripsi' => 'Timbangan gantung untuk keperluan pasar',
                'satuan' => 'kg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Takaran Beras',
                'kode' => 'TB',
                'deskripsi' => 'Alat takaran khusus untuk beras dan biji-bijian',
                'satuan' => 'liter',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($jenisUttp as $jenis) {
            JenisUTTP::create($jenis);
        }
    }
}
