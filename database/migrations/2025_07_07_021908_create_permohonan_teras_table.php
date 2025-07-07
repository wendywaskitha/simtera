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
        Schema::create('permohonan_teras', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_permohonan', 20)->unique();
            $table->foreignId('uttp_id')->constrained('uttps')->onDelete('cascade');
            $table->enum('jenis_layanan', ['Di Kantor', 'Luar Kantor', 'Sidang Tera']);
            $table->enum('status', ['Pending', 'Disetujui', 'Dijadwalkan', 'Selesai', 'Ditolak'])->default('Pending');
            $table->date('tanggal_permohonan');
            $table->date('tanggal_jadwal')->nullable();
            $table->text('catatan_pemohon')->nullable();
            $table->text('catatan_petugas')->nullable();
            $table->json('dokumen_pendukung')->nullable(); // Array path file
            $table->string('petugas_assigned', 100)->nullable();
            $table->timestamps();
            
            $table->index(['status', 'tanggal_permohonan']);
            $table->index(['jenis_layanan', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_teras');
    }
};
