<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Petugas;

class PetugasSeeder extends Seeder
{
    public function run(): void
    {
        $petugas = [
            [
                'nip' => '196801011990031001',
                'nama' => 'Drs. Ahmad Syarifuddin, M.Si',
                'jabatan' => 'Kepala UPTD Metrologi Legal',
                'telepon' => '081234567890',
                'email' => 'kepala@uptd-munbar.go.id',
                'alamat' => 'Jl. Poros Raha-Baubau, Kec. Sawerigadi, Kab. Muna Barat',
                'kompetensi' => json_encode(['Timbangan', 'Takaran', 'Ukuran', 'Anak Timbangan']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '197205151998021001',
                'nama' => 'Ir. Muhammad Ridwan',
                'jabatan' => 'Petugas Tera Senior',
                'telepon' => '081234567891',
                'email' => 'petugas1@uptd-munbar.go.id',
                'alamat' => 'Jl. Masjid Raya, Kec. Lawa, Kab. Muna Barat',
                'kompetensi' => json_encode(['Timbangan', 'Takaran']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '198003102005021002',
                'nama' => 'S.T. Andi Kurniawan',
                'jabatan' => 'Petugas Tera',
                'telepon' => '081234567892',
                'email' => 'petugas2@uptd-munbar.go.id',
                'alamat' => 'Jl. Pendidikan, Kec. Kusambi, Kab. Muna Barat',
                'kompetensi' => json_encode(['Ukuran', 'Anak Timbangan']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '198506152010012001',
                'nama' => 'Siti Nurhaliza, S.Kom',
                'jabatan' => 'Petugas Tera',
                'telepon' => '081234567893',
                'email' => 'petugas3@uptd-munbar.go.id',
                'alamat' => 'Jl. Pemuda, Kec. Tiworo Kepulauan, Kab. Muna Barat',
                'kompetensi' => json_encode(['Timbangan', 'Ukuran']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '199001052015031001',
                'nama' => 'Muh. Fadli Rahman, A.Md',
                'jabatan' => 'Staff Administrasi',
                'telepon' => '081234567894',
                'email' => 'staff@uptd-munbar.go.id',
                'alamat' => 'Jl. Pahlawan, Kec. Maginti, Kab. Muna Barat',
                'kompetensi' => json_encode(['Administrasi']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($petugas as $p) {
            Petugas::create($p);
        }
    }
}
