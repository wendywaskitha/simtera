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
        Schema::create('hasil_teras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_tera_id')->constrained('permohonan_teras')->onDelete('cascade');
            $table->foreignId('uttp_id')->constrained('uttps')->onDelete('cascade');
            $table->enum('hasil', ['Sah', 'Batal', 'Rusak', 'Tidak Layak']);
            $table->date('tanggal_tera');
            $table->string('nomor_sertifikat', 50)->unique()->nullable();
            $table->date('tanggal_expired')->nullable();
            $table->string('petugas_tera', 100);
            $table->text('catatan_hasil')->nullable();
            $table->json('foto_hasil')->nullable(); // Array path foto
            $table->timestamps();

            $table->index(['hasil', 'tanggal_tera']);
            $table->index(['tanggal_expired', 'hasil']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_teras');
    }
};
