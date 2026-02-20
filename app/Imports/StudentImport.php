<?php

namespace App\Imports;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private int $rowCount = 0;

    public function model(array $row)
    {
        $this->rowCount++;

        // Cari classroom berdasarkan nama kelas dari kolom 'kelas' di Excel
        $classroom = Classroom::where('name', $row['kelas'])->first();

        return new Student([
            'nisn'         => $row['nisn'],
            'name'         => $row['nama'],
            'classroom_id' => $classroom?->id,
            'absen'        => $row['absen'] ?? null,
            'username'     => $row['username'] ?? $row['nisn'],
            'password'     => Hash::make($row['password'] ?? $row['nisn']),
            'phone'        => $row['telepon'] ?? null,
            'address'      => $row['alamat'] ?? null,
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
