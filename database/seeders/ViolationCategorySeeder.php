<?php

namespace Database\Seeders;

use App\Models\ViolationCategory;
use Illuminate\Database\Seeder;

class ViolationCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'code' => 'A',
                'name' => 'KERAJINAN',
                'description' => 'Pelanggaran terkait kerajinan dan kedisiplinan waktu',
                'order' => 1,
            ],
            [
                'code' => 'B',
                'name' => 'KERAPIAN',
                'description' => 'Pelanggaran terkait kerapian dan penampilan',
                'order' => 2,
            ],
            [
                'code' => 'C',
                'name' => 'SIKAP PERILAKU',
                'description' => 'Pelanggaran terkait sikap dan perilaku',
                'order' => 3,
            ],
        ];

        foreach ($categories as $category) {
            ViolationCategory::create($category);
        }
    }
}
