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
        Schema::table('users', function (Blueprint $table) {
            // Remove email columns
            $table->dropColumn(['email', 'email_verified_at']);
            
            // Add username
            $table->string('username')->unique()->after('name');
            
            // Add role and bidang relationships
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
            $table->foreignId('bidang_id')->nullable()->constrained('bidangs')->onDelete('set null');
            
            // Add status
            $table->enum('status', ['active', 'not active'])->default('active')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back email columns
            $table->string('email')->unique()->after('name');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            
            // Remove added columns
            $table->dropForeign(['role_id']);
            $table->dropForeign(['bidang_id']);
            $table->dropColumn(['username', 'role_id', 'bidang_id', 'status']);
        });
    }
};