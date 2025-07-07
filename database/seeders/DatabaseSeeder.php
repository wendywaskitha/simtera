<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\DesaSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\UTTPSeeder;
use Database\Seeders\PasarSeeder;
use Database\Seeders\PetugasSeeder;
use Database\Seeders\JenisUttpSeeder;
use Database\Seeders\KecamatanSeeder;
use Database\Seeders\PermohonanTeraSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            UserSeeder::class,
            PetugasSeeder::class,
            KecamatanSeeder::class,
            DesaSeeder::class,
            JenisUttpSeeder::class,
            PasarSeeder::class,
            UTTPSeeder::class,
            PermohonanTeraSeeder::class,

        ]);
    }
}
