<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateUserPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereNull('permissions')->get();
        
        foreach ($users as $user) {
            $permissions = match($user->role) {
                'admin' => ['view_any', 'create', 'update', 'delete', 'manage_users', 'view_reports'],
                'kepala' => ['view_any', 'view_reports', 'approve_requests'],
                'petugas' => ['view_any', 'create', 'update', 'input_results'],
                'staff' => ['view_any', 'create', 'update'],
                'user' => ['view_any'],
                default => []
            };
            
            $user->update(['permissions' => $permissions]);
        }
    }
}
