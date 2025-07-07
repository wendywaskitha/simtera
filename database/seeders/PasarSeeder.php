<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pasar;
use App\Models\Desa;
use App\Models\Kecamatan;

class PasarSeeder extends Seeder
{
    public function run(): void
    {
        $pasarData = [
            // Kecamatan Barangka
            [
                'nama' => 'Pasar Barangka',
                'kecamatan' => 'Barangka',
                'desa' => 'Barangka',
                'alamat' => 'Jl. Pasar Barangka, Desa Barangka',
                'latitude' => -5.1234567,
                'longitude' => 122.1234567,
                'kontak_person' => 'Bapak Suryanto',
                'telepon' => '081234567801',
                'keterangan' => 'Pasar tradisional utama di Kecamatan Barangka, beroperasi setiap hari',
            ],
            [
                'nama' => 'Pasar Bungkolo',
                'kecamatan' => 'Barangka',
                'desa' => 'Bungkolo',
                'alamat' => 'Desa Bungkolo, Kecamatan Barangka',
                'latitude' => -5.1345678,
                'longitude' => 122.1345678,
                'kontak_person' => 'Ibu Siti Aminah',
                'telepon' => '081234567802',
                'keterangan' => 'Pasar desa dengan aktivitas perdagangan setiap hari Selasa dan Jumat',
            ],

            // Kecamatan Kusambi
            [
                'nama' => 'Pasar Kusambi',
                'kecamatan' => 'Kusambi',
                'desa' => 'Kusambi',
                'alamat' => 'Jl. Trans Sulawesi, Desa Kusambi',
                'latitude' => -5.2234567,
                'longitude' => 122.2234567,
                'kontak_person' => 'Bapak Ahmad Yusuf',
                'telepon' => '081234567803',
                'keterangan' => 'Pasar regional yang melayani beberapa desa di sekitar Kusambi',
            ],
            [
                'nama' => 'Pasar Tanjung Pinang',
                'kecamatan' => 'Kusambi',
                'desa' => 'Tanjung Pinang',
                'alamat' => 'Desa Tanjung Pinang, Kecamatan Kusambi',
                'latitude' => -5.2345678,
                'longitude' => 122.2345678,
                'kontak_person' => 'Ibu Ratna Sari',
                'telepon' => '081234567804',
                'keterangan' => 'Pasar pesisir dengan komoditas ikan dan hasil laut',
            ],

            // Kecamatan Lawa
            [
                'nama' => 'Pasar Lawa',
                'kecamatan' => 'Lawa',
                'desa' => 'Lagadi',
                'alamat' => 'Desa Lagadi, Kecamatan Lawa',
                'latitude' => -5.3234567,
                'longitude' => 122.3234567,
                'kontak_person' => 'Bapak La Ode Ruslan',
                'telepon' => '081234567805',
                'keterangan' => 'Pasar sentral Kecamatan Lawa dengan berbagai komoditas pertanian',
            ],
            [
                'nama' => 'Pasar Watumela',
                'kecamatan' => 'Lawa',
                'desa' => 'Watumela',
                'alamat' => 'Desa Watumela, Kecamatan Lawa',
                'latitude' => -5.3345678,
                'longitude' => 122.3345678,
                'kontak_person' => 'Ibu Wa Ode Sari',
                'telepon' => '081234567806',
                'keterangan' => 'Pasar desa dengan aktivitas perdagangan 3 kali seminggu',
            ],

            // Kecamatan Maginti
            [
                'nama' => 'Pasar Maginti',
                'kecamatan' => 'Maginti',
                'desa' => 'Maginti',
                'alamat' => 'Jl. Pasar Maginti, Desa Maginti',
                'latitude' => -5.4234567,
                'longitude' => 122.4234567,
                'kontak_person' => 'Bapak Usman Hadi',
                'telepon' => '081234567807',
                'keterangan' => 'Pasar utama Kecamatan Maginti, beroperasi setiap hari',
            ],
            [
                'nama' => 'Pasar Abadi Jaya',
                'kecamatan' => 'Maginti',
                'desa' => 'Abadi Jaya',
                'alamat' => 'Desa Abadi Jaya, Kecamatan Maginti',
                'latitude' => -5.4345678,
                'longitude' => 122.4345678,
                'kontak_person' => 'Ibu Nurlaela',
                'telepon' => '081234567808',
                'keterangan' => 'Pasar desa dengan fokus pada hasil pertanian dan perkebunan',
            ],

            // Kecamatan Napano Kusambi
            [
                'nama' => 'Pasar Napano',
                'kecamatan' => 'Napano Kusambi',
                'desa' => 'Kombikuno',
                'alamat' => 'Desa Kombikuno, Kecamatan Napano Kusambi',
                'latitude' => -5.5234567,
                'longitude' => 122.5234567,
                'kontak_person' => 'Bapak Syamsul Bahri',
                'telepon' => '081234567809',
                'keterangan' => 'Pasar kecamatan dengan komoditas utama hasil pertanian',
            ],

            // Kecamatan Sawerigadi (Ibukota Kabupaten)
            [
                'nama' => 'Pasar Sentral Raha',
                'kecamatan' => 'Sawerigadi',
                'desa' => 'Kampobalano',
                'alamat' => 'Jl. Pasar Sentral, Kampobalano, Raha',
                'latitude' => -4.9234567,
                'longitude' => 122.6234567,
                'kontak_person' => 'Bapak H. Abdul Rahman',
                'telepon' => '081234567810',
                'keterangan' => 'Pasar terbesar di Kabupaten Muna Barat, beroperasi 24 jam',
            ],
            [
                'nama' => 'Pasar Marobea',
                'kecamatan' => 'Sawerigadi',
                'desa' => 'Marobea',
                'alamat' => 'Desa Marobea, Kecamatan Sawerigadi',
                'latitude' => -4.9345678,
                'longitude' => 122.6345678,
                'kontak_person' => 'Ibu Hj. Fatimah',
                'telepon' => '081234567811',
                'keterangan' => 'Pasar regional dengan berbagai fasilitas modern',
            ],
            [
                'nama' => 'Pasar Lawada Jaya',
                'kecamatan' => 'Sawerigadi',
                'desa' => 'Lawada Jaya',
                'alamat' => 'Desa Lawada Jaya, Kecamatan Sawerigadi',
                'latitude' => -4.9456789,
                'longitude' => 122.6456789,
                'kontak_person' => 'Bapak La Ode Saiful',
                'telepon' => '081234567812',
                'keterangan' => 'Pasar desa dengan spesialisasi produk pertanian organik',
            ],

            // Kecamatan Tiworo Kepulauan
            [
                'nama' => 'Pasar Katela',
                'kecamatan' => 'Tiworo Kepulauan',
                'desa' => 'Katela',
                'alamat' => 'Desa Katela, Kecamatan Tiworo Kepulauan',
                'latitude' => -5.6234567,
                'longitude' => 122.7234567,
                'kontak_person' => 'Bapak Amir Hamzah',
                'telepon' => '081234567813',
                'keterangan' => 'Pasar kepulauan dengan komoditas utama hasil laut',
            ],
            [
                'nama' => 'Pasar Wulanga Jaya',
                'kecamatan' => 'Tiworo Kepulauan',
                'desa' => 'Wulanga Jaya',
                'alamat' => 'Desa Wulanga Jaya, Kecamatan Tiworo Kepulauan',
                'latitude' => -5.6345678,
                'longitude' => 122.7345678,
                'kontak_person' => 'Ibu Wa Ode Rina',
                'telepon' => '081234567814',
                'keterangan' => 'Pasar pesisir dengan aktivitas perdagangan ikan segar',
            ],

            // Kecamatan Tiworo Selatan
            [
                'nama' => 'Pasar Sangia Tiworo',
                'kecamatan' => 'Tiworo Selatan',
                'desa' => 'Sangia Tiworo',
                'alamat' => 'Desa Sangia Tiworo, Kecamatan Tiworo Selatan',
                'latitude' => -5.7234567,
                'longitude' => 122.8234567,
                'kontak_person' => 'Bapak Muh. Saleh',
                'telepon' => '081234567815',
                'keterangan' => 'Pasar kecamatan dengan komoditas pertanian dan peternakan',
            ],

            // Kecamatan Tiworo Tengah
            [
                'nama' => 'Pasar Mekar Jaya',
                'kecamatan' => 'Tiworo Tengah',
                'desa' => 'Mekar Jaya',
                'alamat' => 'Desa Mekar Jaya, Kecamatan Tiworo Tengah',
                'latitude' => -5.8234567,
                'longitude' => 122.9234567,
                'kontak_person' => 'Ibu Sri Wahyuni',
                'telepon' => '081234567816',
                'keterangan' => 'Pasar desa dengan fokus pada produk pertanian lokal',
            ],
            [
                'nama' => 'Pasar Suka Damai',
                'kecamatan' => 'Tiworo Tengah',
                'desa' => 'Suka Damai',
                'alamat' => 'Desa Suka Damai, Kecamatan Tiworo Tengah',
                'latitude' => -5.8345678,
                'longitude' => 122.9345678,
                'kontak_person' => 'Bapak Irwan Setiawan',
                'telepon' => '081234567817',
                'keterangan' => 'Pasar transmigran dengan berbagai komoditas',
            ],

            // Kecamatan Tiworo Utara
            [
                'nama' => 'Pasar Bero',
                'kecamatan' => 'Tiworo Utara',
                'desa' => 'Bero',
                'alamat' => 'Desa Bero, Kecamatan Tiworo Utara',
                'latitude' => -5.9234567,
                'longitude' => 123.0234567,
                'kontak_person' => 'Bapak La Ode Muh. Amin',
                'telepon' => '081234567818',
                'keterangan' => 'Pasar utama Kecamatan Tiworo Utara',
            ],
            [
                'nama' => 'Pasar Santigi',
                'kecamatan' => 'Tiworo Utara',
                'desa' => 'Santigi',
                'alamat' => 'Desa Santigi, Kecamatan Tiworo Utara',
                'latitude' => -5.9345678,
                'longitude' => 123.0345678,
                'kontak_person' => 'Ibu Wa Ode Sumiati',
                'telepon' => '081234567819',
                'keterangan' => 'Pasar desa dengan aktivitas perdagangan mingguan',
            ],

            // Kecamatan Wadaga
            [
                'nama' => 'Pasar Katobu',
                'kecamatan' => 'Wadaga',
                'desa' => 'Katobu',
                'alamat' => 'Desa Katobu, Kecamatan Wadaga',
                'latitude' => -5.0234567,
                'longitude' => 123.1234567,
                'kontak_person' => 'Bapak H. Muh. Yusuf',
                'telepon' => '081234567820',
                'keterangan' => 'Pasar regional dengan fasilitas lengkap',
            ],
            [
                'nama' => 'Pasar Lailangga',
                'kecamatan' => 'Wadaga',
                'desa' => 'Lailangga',
                'alamat' => 'Desa Lailangga, Kecamatan Wadaga',
                'latitude' => -5.0345678,
                'longitude' => 123.1345678,
                'kontak_person' => 'Ibu Hj. Rosmini',
                'telepon' => '081234567821',
                'keterangan' => 'Pasar desa dengan komoditas hasil pertanian dan kerajinan',
            ],
        ];

        foreach ($pasarData as $pasar) {
            // Cari desa berdasarkan nama kecamatan dan nama desa
            $kecamatan = Kecamatan::where('nama', $pasar['kecamatan'])->first();
            
            if ($kecamatan) {
                $desa = Desa::where('nama', $pasar['desa'])
                           ->where('kecamatan_id', $kecamatan->id)
                           ->first();
                
                if ($desa) {
                    Pasar::create([
                        'nama' => $pasar['nama'],
                        'desa_id' => $desa->id,
                        'alamat' => $pasar['alamat'],
                        'latitude' => $pasar['latitude'],
                        'longitude' => $pasar['longitude'],
                        'kontak_person' => $pasar['kontak_person'],
                        'telepon' => $pasar['telepon'],
                        'keterangan' => $pasar['keterangan'],
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // Log jika desa tidak ditemukan
                    \Log::warning("Desa '{$pasar['desa']}' di Kecamatan '{$pasar['kecamatan']}' tidak ditemukan untuk pasar '{$pasar['nama']}'");
                }
            } else {
                // Log jika kecamatan tidak ditemukan
                \Log::warning("Kecamatan '{$pasar['kecamatan']}' tidak ditemukan untuk pasar '{$pasar['nama']}'");
            }
        }
    }
}
