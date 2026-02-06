<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_budgets', function (Blueprint $table) {
            $table->string('no_surat')->nullable()->after('jenis_pengeluaran');
            $table->integer('jumlah_anggota')->nullable()->after('no_surat');
            $table->string('nama_aksi')->nullable()->after('jumlah_anggota');
            $table->string('tempat_aksi')->nullable()->after('nama_aksi');
            $table->time('jam_aksi')->nullable()->after('tempat_aksi');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_budgets', function (Blueprint $table) {
            $table->dropColumn(['no_surat', 'jumlah_anggota', 'nama_aksi', 'tempat_aksi', 'jam_aksi']);
        });
    }
};