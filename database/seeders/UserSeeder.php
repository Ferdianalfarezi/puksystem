<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Bidang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Super Admin role and first bidang
        $superAdminRole = Role::where('nama', 'Super Admin')->first();
        $firstBidang = Bidang::first();

        // Create Super Admin user
        User::create([
            'name' => 'Super Administrator',
            'username' => 'superadmin',
            'password' => Hash::make('password'),
            'role_id' => $superAdminRole->id,
            'bidang_id' => $firstBidang->id,
            'status' => 'active'
        ]);

        // Create sample users for each role
        $adminRole = Role::where('nama', 'Admin')->first();
        $ketuaRole = Role::where('nama', 'Ketua')->first();
        
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'bidang_id' => $firstBidang->id,
            'status' => 'active'
        ]);

        User::create([
            'name' => 'Ketua Organisasi',
            'username' => 'ketua',
            'password' => Hash::make('password'),
            'role_id' => $ketuaRole->id,
            'bidang_id' => $firstBidang->id,
            'status' => 'active'
        ]);
    }
}