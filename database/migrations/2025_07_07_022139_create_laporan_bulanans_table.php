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
        Schema::create('laporan_bulanans', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan');
            $table->integer('total_uttp_terdaftar')->default(0);
            $table->integer('total_tera_dilakukan')->default(0);
            $table->integer('total_tera_lulus')->default(0);
            $table->integer('total_tera_tidak_lulus')->default(0);
            $table->integer('total_permohonan')->default(0);
            $table->json('detail_per_jenis')->nullable(); // Breakdown per jenis UTTP
            $table->json('detail_per_lokasi')->nullable(); // Breakdown per kecamatan
            $table->timestamps();
            
            $table->unique(['tahun', 'bulan']);
            $table->index(['tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_bulanans');
    }
};
