<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_hutang', function (Blueprint $table) {
            $table->id();
            
            // Data Peminjam
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('bidang_id')->constrained('bidangs')->onDelete('cascade');
            $table->string('nama'); // Nama user (redundant untuk history)
            
            // Data Pinjaman
            $table->decimal('jumlah', 15, 2);
            $table->decimal('sisa_hutang', 15, 2); // Default = jumlah
            $table->text('keperluan');
            $table->date('tanggal');
            $table->year('tahun');
            
            // Status & Workflow
            $table->enum('status', [
                'draft',
                'menunggu_konfirmasi_bendahara',
                'menunggu_approval_ketua',
                'menunggu_pencairan',
                'dicairkan', // Hutang aktif
                'lunas', // Sudah lunas
                'ditolak_bendahara',
                'ditolak_ketua'
            ])->default('draft');
            
            // Submission
            $table->foreignId('submitted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            
            // Bendahara Review
            $table->foreignId('reviewed_by_bendahara')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at_bendahara')->nullable();
            $table->text('catatan_bendahara')->nullable();
            
            // Ketua Review
            $table->foreignId('reviewed_by_ketua')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at_ketua')->nullable();
            $table->text('catatan_ketua')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('user_id');
            $table->index(['tahun', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_hutang');
    }
};