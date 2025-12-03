<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_hutang_history', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('pengajuan_hutang_id')->constrained('pengajuan_hutang')->onDelete('cascade');
            
            // Status Change
            $table->string('status_dari')->nullable();
            $table->string('status_ke');
            
            // Details
            $table->text('catatan')->nullable();
            $table->json('data_snapshot')->nullable(); // Store snapshot of data
            
            // Who & When
            $table->foreignId('dilakukan_oleh')->constrained('users')->onDelete('cascade');
            $table->timestamp('dilakukan_pada');
            
            $table->timestamps();
            
            // Indexes
            $table->index('pengajuan_hutang_id');
            $table->index('dilakukan_pada');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_hutang_history');
    }
};