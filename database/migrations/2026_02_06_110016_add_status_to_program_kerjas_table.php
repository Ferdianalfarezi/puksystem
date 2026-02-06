<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_kerjas', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('tahun');
            // Status: draft, menunggu_konfirmasi_bendahara, menunggu_approval_ketua, 
            //         menunggu_pencairan, dicairkan, ditolak_bendahara, ditolak_ketua
        });
    }

    public function down(): void
    {
        Schema::table('program_kerjas', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};