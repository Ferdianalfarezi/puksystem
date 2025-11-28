<?php

namespace Database\Seeders;

use App\Models\Bidang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bidangs = [
            [
                'nama' => 'Bidang Organisasi',
                'deskripsi' => 'Mengelola struktur organisasi, keanggotaan, dan tata kelola internal'
            ],
            [
                'nama' => 'Bidang Pendidikan',
                'deskripsi' => 'Mengelola program pendidikan, pelatihan, dan pengembangan kapasitas'
            ],
            [
                'nama' => 'Bidang Hubungan Industrial',
                'deskripsi' => 'Mengelola hubungan industrial, negosiasi, dan advokasi kebijakan'
            ],
            [
                'nama' => 'Bidang Sosial Ekonomi',
                'deskripsi' => 'Mengelola program sosial, ekonomi, dan kesejahteraan anggota'
            ],
            [
                'nama' => 'Bidang Upah dan Bonus',
                'deskripsi' => 'Mengelola negosiasi upah, bonus, dan tunjangan anggota'
            ]
        ];

        foreach ($bidangs as $bidang) {
            Bidang::create($bidang);
        }
    }
}