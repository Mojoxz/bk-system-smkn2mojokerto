<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $classes = ['X RPL 1', 'X RPL 2', 'XI RPL 1', 'XI RPL 2', 'XII RPL 1', 'XII RPL 2'];

        for ($i = 1; $i <= 20; $i++) {
            Student::create([
                'nisn' => '000000000' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'name' => 'Siswa ' . $i,
                'class' => $classes[array_rand($classes)],
                'absen' => str_pad($i % 36 + 1, 2, '0', STR_PAD_LEFT),
                'username' => 'siswa' . $i,
                'password' => Hash::make('password'),
                'phone' => '0812345678' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'address' => 'Mojokerto',
            ]);
        }
    }
}
