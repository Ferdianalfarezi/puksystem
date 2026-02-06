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
        // ✅ Cek dulu ada apa kagak
        $superAdminRole = Role::where('nama', 'Super Admin')->first();
        $firstBidang = Bidang::first();

        // ✅ Kalo ga ada, bikin dulu atau kasih error
        if (!$superAdminRole) {
            $this->command->error('Role Super Admin belum ada! Jalanin RoleSeeder dulu.');
            return;
        }

        if (!$firstBidang) {
            $this->command->error('Bidang belum ada! Jalanin BidangSeeder dulu.');
            return;
        }

        // Create Super Admin user
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole->id,
                'bidang_id' => $firstBidang->id,
                'status' => 'active'
            ]
        );

        // Create sample users for each role
        $adminRole = Role::where('nama', 'Admin')->first();
        $bendaharaRole = Role::where('nama', 'Bendahara')->first();
        $ketuaRole = Role::where('nama', 'Ketua')->first();
        
        if ($adminRole) {
            User::updateOrCreate(
                ['username' => 'admin'],
                [
                    'name' => 'Admin User',
                    'password' => Hash::make('password'),
                    'role_id' => $adminRole->id,
                    'bidang_id' => $firstBidang->id,
                    'status' => 'active'
                ]
            );
        }

        if ($bendaharaRole) {
            User::updateOrCreate(
                ['username' => 'bendahara'],
                [
                    'name' => 'Bendahara Organisasi',
                    'password' => Hash::make('password'),
                    'role_id' => $bendaharaRole->id,
                    'bidang_id' => $firstBidang->id,
                    'status' => 'active'
                ]
            );
        }

        if ($ketuaRole) {
            User::updateOrCreate(
                ['username' => 'ketua'],
                [
                    'name' => 'Ketua Organisasi',
                    'password' => Hash::make('password'),
                    'role_id' => $ketuaRole->id,
                    'bidang_id' => $firstBidang->id,
                    'status' => 'active'
                ]
            );
        }

        $this->command->info('Users created successfully!');
    }
}