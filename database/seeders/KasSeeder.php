<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kas;

class KasSeeder extends Seeder
{
    public function run(): void
    {
        Kas::create([
            'id' => 1,
            'saldo' => 0,
            'keterangan' => 'Kas Global',
        ]);
    }
}