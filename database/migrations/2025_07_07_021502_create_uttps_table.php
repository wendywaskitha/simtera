<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uttps', function (Blueprint $table) {
            $table->id();
            $table->string('kode_uttp', 20)->unique();

            // Relasi ke pemilik (normalisasi)
            $table->foreignId('pemilik_id')->constrained('pemiliks')->onDelete('restrict');

            // Data UTTP
            $table->foreignId('jenis_uttp_id')->constrained('jenis_uttps')->onDelete('restrict');
            $table->string('merk', 50)->nullable();
            $table->string('tipe', 50)->nullable();
            $table->string('nomor_seri', 50)->unique();
            $table->decimal('kapasitas_maksimum', 12, 3)->nullable();
            $table->decimal('daya_baca', 12, 6)->nullable();
            $table->year('tahun_pembuatan')->nullable();

            // Lokasi
            $table->foreignId('desa_id')->constrained('desas')->onDelete('restrict');
            $table->text('alamat_lengkap');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('lokasi_type', ['Pasar', 'Luar Pasar']);
            $table->string('detail_lokasi', 100)->nullable();

            // Status Tera
            $table->enum('status_tera', ['Belum Tera', 'Aktif', 'Expired', 'Rusak', 'Tidak Layak'])->default('Belum Tera');
            $table->date('tanggal_tera_terakhir')->nullable();
            $table->date('tanggal_expired')->nullable();
            $table->string('nomor_sertifikat', 50)->nullable();
            $table->string('petugas_tera', 100)->nullable();

            // Dokumentasi
            $table->json('foto_uttp')->nullable();
            $table->text('keterangan')->nullable();

            // System fields
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes untuk optimasi query
            $table->index(['pemilik_id', 'status_tera']);
            $table->index(['status_tera', 'is_active']);
            $table->index(['jenis_uttp_id', 'status_tera']);
            $table->index(['desa_id', 'lokasi_type']);
            $table->index(['tanggal_expired', 'status_tera']);
            $table->index(['created_at', 'status_tera']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uttps');
    }
};
