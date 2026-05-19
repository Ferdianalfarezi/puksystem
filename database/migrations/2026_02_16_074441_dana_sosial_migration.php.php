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
        // Tabel utama - data aktif/ongoing
        Schema::create('dana_sosials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koorlap_id')->constrained('koorlaps')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('jenis', ['rawat_inap', 'duka_cita', 'banjir']);
            $table->decimal('nominal', 15, 2);
            $table->string('evident')->nullable();
            $table->enum('status', [
                'menunggu_persetujuan_bidang_sosial',
                'disetujui',
                'ditolak',
                'diserahkan'
            ])->default('menunggu_persetujuan_bidang_sosial');
            
            // Approval oleh Bidang Sosial
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('catatan_approval')->nullable();
            
            // Verifikasi final oleh Koorlap
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();
        });

        // Tabel history - data yang sudah selesai (diserahkan/ditolak)
        Schema::create('dana_sosial_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dana_sosial_id')->nullable(); // Reference ID asli
            $table->foreignId('koorlap_id')->constrained('koorlaps')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('jenis', ['rawat_inap', 'duka_cita', 'banjir']);
            $table->decimal('nominal', 15, 2);
            $table->string('evident')->nullable();
            $table->enum('status', [
                'menunggu_persetujuan_bidang_sosial',
                'disetujui',
                'ditolak',
                'diserahkan'
            ]);
            
            // Approval oleh Bidang Sosial
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('catatan_approval')->nullable();
            
            // Verifikasi final oleh Koorlap
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            
            // Timestamp completion
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('original_created_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dana_sosial_histories');
        Schema::dropIfExists('dana_sosials');
    }
};