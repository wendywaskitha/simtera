<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_uttps', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique();
            $table->string('kode', 10)->unique()->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('satuan', 20)->nullable(); // kg, liter, meter, dll
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index untuk optimasi query
            $table->index(['is_active', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_uttps');
    }
};
