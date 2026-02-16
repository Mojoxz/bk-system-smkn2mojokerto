<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function query()
    {
        return Student::query()->with('violations')->orderBy('class')->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'NISN',
            'Nama',
            'Kelas',
            'Absen',
            'Username',
            'Telepon',
            'Alamat',
            'Total Poin',
            'Jumlah Pelanggaran',
            'Status',
        ];
    }

    public function map($student): array
    {
        return [
            $student->nisn,
            $student->name,
            $student->class,
            $student->absen,
            $student->username,
            $student->phone,
            $student->address,
            $student->total_points,
            $student->violations()->where('status', 'approved')->count(),
            $student->is_active ? 'Aktif' : 'Tidak Aktif',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
