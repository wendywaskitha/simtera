<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('desas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->onDelete('cascade');
            $table->string('nama', 100);
            $table->string('kode', 15)->unique()->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index untuk optimasi query
            $table->index(['kecamatan_id', 'is_active']);
            $table->index(['nama', 'kecamatan_id']);
            
            // Unique constraint untuk nama desa per kecamatan
            $table->unique(['kecamatan_id', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desas');
    }
};
