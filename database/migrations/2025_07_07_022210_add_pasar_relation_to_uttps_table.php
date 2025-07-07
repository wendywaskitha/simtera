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
        Schema::table('uttps', function (Blueprint $table) {
            $table->foreignId('pasar_id')->nullable()->after('desa_id')->constrained('pasars')->onDelete('set null');
            $table->index(['pasar_id', 'lokasi_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uttps', function (Blueprint $table) {
            //
        });
    }
};
