<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Desa;
use App\Models\Kecamatan;

class DesaSeeder extends Seeder
{
    public function run(): void
    {
        $desaData = [
            'Barangka' => ['Barangka', 'Bungkolo', 'Lafinde', 'Lapolea', 'Sawerigadi', 'Walelei', 'Waulai', 'Wuna'],
            'Kusambi' => ['Bakeramba', 'Guali', 'Kasakamu', 'Kusambi', 'Lakawoghe', 'Lapokainse', 'Lemoambo', 'Sidamangura', 'Tanjung Pinang'],
            'Lawa' => ['Lagadi', 'Lalemba', 'Latompe', 'Latugho', 'Madampi', 'Watumela'],
            'Maginti' => ['Abadi Jaya', 'Bangko', 'Gala', 'Kangkunawe', 'Kembar Maminasa', 'Maginti', 'Pajala', 'Pasipadangan'],
            'Napano Kusambi' => ['Kombikuno', 'Lahaji', 'Latawe', 'Masara', 'Tangkumaho', 'Umba'],
            'Sawerigadi' => ['Kampobalano', 'Lakalamba', 'Lawada Jaya', 'Lombu Jaya', 'Maperaha', 'Marobea', 'Nihi', 'Ondoke', 'Wakoila', 'Waukuni'],
            'Tiworo Kepulauan' => ['Katela', 'Lasama', 'Laworo', 'Sidomakmur', 'Wandoke', 'Waturempe', 'Wulanga Jaya'],
            'Tiworo Selatan' => ['Barakkah', 'Kasimpa Jaya', 'Katangana', 'Parura Jaya', 'Sangia Tiworo'],
            'Tiworo Tengah' => ['Labokolo', 'Lakabu', 'Langku Langku', 'Mekar Jaya', 'Momuntu', 'Suka Damai', 'Wanseriwu', 'Wapae'],
            'Tiworo Utara' => ['Bero', 'Mandike', 'Santigi', 'Santiri', 'Tasipi', 'Tiga', 'Tondasi'],
            'Wadaga' => ['Kampani', 'Katobu', 'Lailangga', 'Lakanaha', 'Lasosodo', 'Lindo', 'Wakontu'],
        ];

        foreach ($desaData as $namaKecamatan => $desas) {
            $kecamatan = Kecamatan::where('nama', $namaKecamatan)->first();
            
            if ($kecamatan) {
                foreach ($desas as $index => $namaDesa) {
                    Desa::create([
                        'kecamatan_id' => $kecamatan->id,
                        'nama' => $namaDesa,
                        'kode' => null,
                        'keterangan' => "Desa {$namaDesa} di Kecamatan {$namaKecamatan}",
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
