<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name' => 'Super Admin',
            'nipd' => '123456789',
            'email' => 'admin@smkn2mojokerto.sch.id',
            'password' => Hash::make('password'),
            'address' => 'SMKN 2 Mojokerto',
            'phone' => '081234567890',
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        Admin::create([
            'name' => 'Guru BK 1',
            'nipd' => '987654321',
            'email' => 'gurubk1@smkn2mojokerto.sch.id',
            'password' => Hash::make('password'),
            'address' => 'Mojokerto',
            'phone' => '081234567891',
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
