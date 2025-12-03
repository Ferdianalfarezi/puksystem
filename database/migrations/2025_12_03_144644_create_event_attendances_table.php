<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nik');
            $table->string('name');
            $table->string('username');
            $table->string('role');
            $table->string('bidang');
            $table->timestamp('waktu_hadir');
            $table->timestamps();
            
            // Prevent duplicate attendance
            $table->unique(['event_id', 'user_id']);
            
            $table->index('event_id');
            $table->index('user_id');
            $table->index('nik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_attendances');
    }
};