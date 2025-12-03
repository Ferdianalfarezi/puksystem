<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('nama_event');
            $table->integer('jumlah_peserta')->default(0);
            $table->date('waktu_pelaksanaan');
            $table->string('tempat_pelaksanaan');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('waktu_pelaksanaan');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};