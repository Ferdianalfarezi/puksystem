<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_hutang', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('pengajuan_hutang_id')->constrained('pengajuan_hutang')->onDelete('cascade');
            
            // Data Pembayaran
            $table->decimal('jumlah_bayar', 15, 2);
            $table->date('tanggal_bayar');
            
            $table->enum('metode_pembayaran', [
                'transfer_bank',
                'tunai',
                'cek',
                'giro'
            ])->default('tunai');
            
            $table->string('nomor_referensi')->nullable();
            $table->text('catatan')->nullable();
            
            // Relasi
            $table->foreignId('dibayar_oleh')->constrained('users')->onDelete('cascade');
            
            // ✅ FIX: Ganti kas_history jadi history_kas (sesuai nama tabel yang ada)
            $table->foreignId('history_kas_id')->nullable()->constrained('history_kas')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes
            $table->index('pengajuan_hutang_id');
            $table->index('tanggal_bayar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_hutang');
    }
};