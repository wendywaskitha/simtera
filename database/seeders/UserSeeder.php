<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator UPTD',
                'email' => 'admin@uptd-munbar.go.id',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
                'phone' => '081234567890',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Kepala UPTD Metrologi Legal',
                'email' => 'kepala@uptd-munbar.go.id',
                'password' => Hash::make('kepala123'),
                'role' => 'kepala',
                'is_active' => true,
                'phone' => '081234567891',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Petugas Tera 1',
                'email' => 'petugas1@uptd-munbar.go.id',
                'password' => Hash::make('petugas123'),
                'role' => 'petugas',
                'is_active' => true,
                'phone' => '081234567892',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Petugas Tera 2',
                'email' => 'petugas2@uptd-munbar.go.id',
                'password' => Hash::make('petugas123'),
                'role' => 'petugas',
                'is_active' => true,
                'phone' => '081234567893',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Staff Administrasi',
                'email' => 'staff@uptd-munbar.go.id',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'is_active' => true,
                'phone' => '081234567894',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
