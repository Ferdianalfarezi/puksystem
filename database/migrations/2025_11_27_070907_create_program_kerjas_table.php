<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_kerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bidang_id')->constrained('bidangs')->onDelete('cascade');
            $table->string('nama');
            $table->decimal('anggaran', 15, 2);
            $table->year('tahun');
            $table->enum('status', [
                'draft',
                'menunggu_konfirmasi_bendahara',
                'menunggu_approval_ketua',
                'ditolak_bendahara',
                'ditolak_ketua',
                'disetujui'
            ])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users');
            $table->foreignId('reviewed_by_bendahara')->nullable()->constrained('users');
            $table->timestamp('reviewed_at_bendahara')->nullable();
            $table->text('catatan_bendahara')->nullable();
            $table->foreignId('reviewed_by_ketua')->nullable()->constrained('users');
            $table->timestamp('reviewed_at_ketua')->nullable();
            $table->text('catatan_ketua')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_kerjas');
    }
};