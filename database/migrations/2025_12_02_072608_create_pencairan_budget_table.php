<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pencairan_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_budget_id')->constrained('pengajuan_budgets')->onDelete('cascade');
            $table->decimal('jumlah_dicairkan', 15, 2);
            $table->timestamp('tanggal_pencairan');
            $table->enum('metode_pencairan', ['transfer_bank', 'tunai', 'cek', 'giro']);
            $table->string('nomor_referensi')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('dicairkan_oleh')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pencairan_budgets');
    }
};