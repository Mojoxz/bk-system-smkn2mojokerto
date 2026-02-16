<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private $rowCount = 0;

    public function model(array $row)
    {
        $this->rowCount++;

        return new Student([
            'nisn' => $row['nisn'],
            'name' => $row['nama'],
            'class' => $row['kelas'],
            'absen' => $row['absen'] ?? null,
            'username' => $row['username'] ?? $row['nisn'],
            'password' => Hash::make($row['password'] ?? $row['nisn']),
            'phone' => $row['telepon'] ?? null,
            'address' => $row['alamat'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nisn' => 'required|unique:students,nisn',
            'nama' => 'required|string',
            'kelas' => 'required|string',
        ];
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
