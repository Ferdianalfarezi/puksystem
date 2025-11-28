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
        Schema::create('pencairans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_kerja_id')->constrained('program_kerjas')->onDelete('cascade');
            $table->decimal('jumlah_dicairkan', 15, 2);
            $table->dateTime('tanggal_pencairan');
            $table->enum('metode_pencairan', ['transfer', 'tunai', 'cek'])->default('transfer');
            $table->string('nomor_referensi')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('dicairkan_oleh')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencairans');
    }
};