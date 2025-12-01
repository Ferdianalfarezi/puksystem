<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_kerjas', function (Blueprint $table) {
            $table->enum('jenis_pengeluaran', [
                'Kesekretariatan',
                'Perjalanan Dinas',
                'Aksi',
                'Dana Sosial',
                'Dansos Duka',
                'Dansos Banjir',
                'Pendidikan',
                'Rapat',
                'COS DPP',
                'Iuaran FKJ',
                'Dansos Ekternal',
                'Iuran GM',
                'Rapat GM'
            ])->after('anggaran')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('program_kerjas', function (Blueprint $table) {
            $table->dropColumn('jenis_pengeluaran');
        });
    }
};