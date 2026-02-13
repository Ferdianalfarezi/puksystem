<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispensasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_budget_id')->constrained('pengajuan_budgets')->onDelete('cascade');
            $table->foreignId('bidang_id')->constrained('bidangs')->onDelete('cascade');
            $table->json('user_ids'); // Array user yang ikut
            $table->text('keterangan')->nullable();
            
            // Status workflow
            $table->enum('status', [
                'draft',
                'menunggu_approval_sekretaris',
                'menunggu_approval_ketua',
                'approved',
                'ditolak_sekretaris',
                'ditolak_ketua'
            ])->default('draft');
            
            // Tracking submission & approval
            $table->foreignId('submitted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            
            $table->foreignId('reviewed_by_sekretaris')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at_sekretaris')->nullable();
            $table->text('catatan_sekretaris')->nullable();
            
            $table->foreignId('reviewed_by_ketua')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at_ketua')->nullable();
            $table->text('catatan_ketua')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispensasis');
    }
};