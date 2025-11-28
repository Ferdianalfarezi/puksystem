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
        Schema::create('program_kerja_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_kerja_id')->constrained('program_kerjas')->onDelete('cascade');
            $table->string('status_dari')->nullable();
            $table->string('status_ke');
            $table->text('catatan')->nullable();
            $table->foreignId('dilakukan_oleh')->constrained('users')->onDelete('cascade');
            $table->dateTime('dilakukan_pada');
            $table->json('data_snapshot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_kerja_histories');
    }
};