<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email')
                  ->comment('User role: admin, petugas, kepala, staff, etc.');
            $table->boolean('is_active')->default(true)->after('role')
                  ->comment('Status aktif user');
            $table->string('phone')->nullable()->after('is_active')
                  ->comment('Nomor telepon pengguna');
            $table->string('profile_photo_path')->nullable()->after('phone')
                  ->comment('Path foto profil pengguna');
            $table->timestamp('last_login_at')->nullable()->after('profile_photo_path')
                  ->comment('Waktu login terakhir');
            
            // Index untuk optimasi query
            $table->index(['role', 'is_active']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'is_active']);
            $table->dropIndex(['is_active']);
            $table->dropColumn([
                'role',
                'is_active', 
                'phone',
                'profile_photo_path',
                'last_login_at'
            ]);
        });
    }
};
