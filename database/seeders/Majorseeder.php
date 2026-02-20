<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    public function run(): void
    {
        $majors = [
            [
                'name' => 'Rekayasa Perangkat Lunak',
                'code' => 'RPL',
            ],
            [
                'name' => 'Desain Komunikasi Visual',
                'code' => 'DKV',
            ],
            [
                'name' => 'Layanan Perbankan Syariah',
                'code' => 'LSP',
            ],
            [
                'name' => 'Agribisnis Pengolahan Hasil Pertanian',
                'code' => 'APHP',
            ],
            [
                'name' => 'Kuliner',
                'code' => 'Kuliner',
            ],
        ];

        foreach ($majors as $major) {
            Major::create([
                'name'        => $major['name'],
                'code'        => $major['code'],
                'description' => null,
                'is_active'   => true,
            ]);
        }
    }
}
