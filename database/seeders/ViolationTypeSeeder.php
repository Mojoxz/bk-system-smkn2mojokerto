<?php

namespace Database\Seeders;

use App\Models\ViolationType;
use App\Models\ViolationCategory;
use Illuminate\Database\Seeder;

class ViolationTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori A - KERAJINAN
        $kerajinan = ViolationCategory::where('code', 'A')->first();

        $kerajinanTypes = [
            ['name' => 'Terlambat masuk sekolah (< 15 menit)', 'points' => 5],
            ['name' => 'Terlambat masuk sekolah (15-30 menit)', 'points' => 10],
            ['name' => 'Terlambat masuk sekolah (> 30 menit)', 'points' => 15],
            ['name' => 'Tidak mengikuti upacara bendera', 'points' => 10],
            ['name' => 'Tidak mengerjakan tugas/PR', 'points' => 5],
            ['name' => 'Tidak membawa buku pelajaran', 'points' => 3],
            ['name' => 'Bolos/membolos pelajaran', 'points' => 25],
            ['name' => 'Tidak masuk tanpa keterangan (1 hari)', 'points' => 20],
        ];

        foreach ($kerajinanTypes as $type) {
            ViolationType::create([
                'violation_category_id' => $kerajinan->id,
                'name' => $type['name'],
                'points' => $type['points'],
                'is_custom' => false,
            ]);
        }

        // Kategori B - KERAPIAN
        $kerapian = ViolationCategory::where('code', 'B')->first();

        $kerapianTypes = [
            ['name' => 'Tidak memakai atribut sekolah lengkap', 'points' => 5],
            ['name' => 'Rambut tidak rapi/panjang (laki-laki)', 'points' => 10],
            ['name' => 'Memakai aksesoris berlebihan', 'points' => 5],
            ['name' => 'Seragam tidak rapi/tidak dimasukkan', 'points' => 5],
            ['name' => 'Kuku panjang/cat kuku', 'points' => 3],
            ['name' => 'Sepatu tidak sesuai ketentuan', 'points' => 5],
            ['name' => 'Kaos kaki tidak sesuai ketentuan', 'points' => 3],
        ];

        foreach ($kerapianTypes as $type) {
            ViolationType::create([
                'violation_category_id' => $kerapian->id,
                'name' => $type['name'],
                'points' => $type['points'],
                'is_custom' => false,
            ]);
        }

        // Kategori C - SIKAP PERILAKU
        $sikap = ViolationCategory::where('code', 'C')->first();

        $sikapTypes = [
            ['name' => 'Tidak sopan kepada guru/staf', 'points' => 25],
            ['name' => 'Berkelahi dengan teman', 'points' => 50],
            ['name' => 'Membawa HP saat KBM berlangsung', 'points' => 15],
            ['name' => 'Merokok di area sekolah', 'points' => 50],
            ['name' => 'Merusak fasilitas sekolah', 'points' => 30],
            ['name' => 'Mencontek saat ujian', 'points' => 20],
            ['name' => 'Ramai/mengganggu saat pelajaran', 'points' => 5],
            ['name' => 'Membawa barang terlarang', 'points' => 75],
            ['name' => 'Berbohong kepada guru', 'points' => 15],
            ['name' => 'Membully teman', 'points' => 40],
        ];

        foreach ($sikapTypes as $type) {
            ViolationType::create([
                'violation_category_id' => $sikap->id,
                'name' => $type['name'],
                'points' => $type['points'],
                'is_custom' => false,
            ]);
        }

        // Tambah satu custom type untuk setiap kategori
        ViolationType::create([
            'violation_category_id' => $kerajinan->id,
            'name' => 'Pelanggaran Kerajinan Lainnya',
            'points' => 0,
            'is_custom' => true,
            'description' => 'Untuk pelanggaran kerajinan yang tidak terdaftar',
        ]);

        ViolationType::create([
            'violation_category_id' => $kerapian->id,
            'name' => 'Pelanggaran Kerapian Lainnya',
            'points' => 0,
            'is_custom' => true,
            'description' => 'Untuk pelanggaran kerapian yang tidak terdaftar',
        ]);

        ViolationType::create([
            'violation_category_id' => $sikap->id,
            'name' => 'Pelanggaran Sikap Lainnya',
            'points' => 0,
            'is_custom' => true,
            'description' => 'Untuk pelanggaran sikap yang tidak terdaftar',
        ]);
    }
}
