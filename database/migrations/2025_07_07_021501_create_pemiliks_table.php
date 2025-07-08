<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemiliks', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('nik', 20)->nullable()->unique();
            $table->string('telepon', 15)->nullable();
            $table->text('alamat');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes untuk optimasi
            $table->index(['nama', 'is_active']);
            $table->index('nik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemiliks');
    }
};
