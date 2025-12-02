<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bidang_id')->constrained('bidangs')->onDelete('cascade');
            $table->string('nama');
            $table->decimal('anggaran', 15, 2);
            $table->string('jenis_pengeluaran');
            $table->year('tahun');
            $table->date('tanggal');
            
            // Status workflow
            $table->enum('status', [
                'draft',
                'menunggu_konfirmasi_bendahara',
                'menunggu_approval_ketua',
                'menunggu_pencairan',
                'dicairkan',
                'ditolak_bendahara',
                'ditolak_ketua'
            ])->default('draft');
            
            // Submission tracking
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Bendahara review
            $table->foreignId('reviewed_by_bendahara')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at_bendahara')->nullable();
            $table->text('catatan_bendahara')->nullable();
            
            // Ketua review
            $table->foreignId('reviewed_by_ketua')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at_ketua')->nullable();
            $table->text('catatan_ketua')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_budgets');
    }
};