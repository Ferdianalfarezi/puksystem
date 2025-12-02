<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_budget_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_budget_id')->constrained('pengajuan_budgets')->onDelete('cascade');
            $table->date('tanggal_pengajuan');
            $table->string('status_dari')->nullable();
            $table->string('status_ke');
            $table->text('catatan')->nullable();
            $table->foreignId('dilakukan_oleh')->constrained('users')->onDelete('cascade');
            $table->timestamp('dilakukan_pada');
            $table->json('data_snapshot')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_budget_histories');
    }
};