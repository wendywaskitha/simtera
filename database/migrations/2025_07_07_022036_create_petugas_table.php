<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('petugas', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 20)->unique();
            $table->string('nama', 100);
            $table->string('jabatan', 50);
            $table->string('telepon', 15)->nullable();
            $table->string('email', 100)->unique()->nullable();
            $table->text('alamat')->nullable();
            $table->json('kompetensi')->nullable(); // Array jenis UTTP yang bisa ditangani
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['is_active', 'nama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petugas');
    }
};
