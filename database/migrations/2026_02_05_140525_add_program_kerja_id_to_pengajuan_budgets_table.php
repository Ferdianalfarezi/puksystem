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
        Schema::table('pengajuan_budgets', function (Blueprint $table) {
            // Tambahkan kolom program_kerja_id setelah kolom jenis
            $table->unsignedBigInteger('program_kerja_id')->nullable()->after('jenis');
            
            // Tambahkan foreign key constraint
            $table->foreign('program_kerja_id')
                  ->references('id')
                  ->on('program_kerjas')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_budgets', function (Blueprint $table) {
            // Hapus foreign key dulu
            $table->dropForeign(['program_kerja_id']);
            // Baru hapus kolom
            $table->dropColumn('program_kerja_id');
        });
    }
};