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
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->text('pesan');
            $table->enum('tipe', ['Email', 'SMS', 'WhatsApp', 'System']);
            $table->string('penerima', 100); // email/phone/user_id
            $table->enum('status', ['Pending', 'Sent', 'Failed'])->default('Pending');
            $table->timestamp('tanggal_kirim')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // Data tambahan
            $table->timestamps();
            
            $table->index(['status', 'tipe']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};
