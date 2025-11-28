<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nama' => 'Super Admin',
                'deskripsi' => 'Administrator dengan akses penuh ke semua fitur sistem'
            ],
            [
                'nama' => 'Admin',
                'deskripsi' => 'Administrator dengan akses terbatas untuk mengelola data'
            ],
            [
                'nama' => 'Ketua',
                'deskripsi' => 'Ketua organisasi dengan akses khusus'
            ],
            [
                'nama' => 'Sekretaris',
                'deskripsi' => 'Sekretaris dengan akses untuk mengelola administrasi'
            ],
            [
                'nama' => 'Bendahara',
                'deskripsi' => 'Bendahara dengan akses untuk mengelola keuangan'
            ],
            [
                'nama' => 'Anggota',
                'deskripsi' => 'Anggota biasa dengan akses terbatas'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}