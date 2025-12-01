<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kas_id')->constrained('kas')->cascadeOnDelete();
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->decimal('jumlah', 15, 2);
            $table->decimal('saldo_sebelum', 15, 2);
            $table->decimal('saldo_sesudah', 15, 2);
            $table->string('sumber'); // 'pencairan', 'setoran'
            $table->unsignedBigInteger('referable_id')->nullable();
            $table->string('referable_type')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('dilakukan_oleh')->constrained('users');
            $table->timestamp('tanggal_transaksi');
            $table->timestamps();
            
            $table->index(['referable_type', 'referable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_kas');
    }
};