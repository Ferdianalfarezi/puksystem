<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pencairans', function (Blueprint $table) {
            $table->foreignId('history_kas_id')->nullable()->after('dicairkan_oleh')->constrained('history_kas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pencairans', function (Blueprint $table) {
            $table->dropForeign(['history_kas_id']);
            $table->dropColumn('history_kas_id');
        });
    }
};