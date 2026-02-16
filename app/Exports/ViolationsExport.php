<?php

namespace App\Exports;

use App\Models\Violation;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ViolationsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Violation::with(['student', 'violationType.category', 'admin']);

        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['category_id'])) {
            $query->whereHas('violationType', function ($q) {
                $q->where('violation_category_id', $this->filters['category_id']);
            });
        }

        if (isset($this->filters['date_from'])) {
            $query->whereDate('violation_date', '>=', $this->filters['date_from']);
        }

        if (isset($this->filters['date_to'])) {
            $query->whereDate('violation_date', '<=', $this->filters['date_to']);
        }

        return $query->orderBy('violation_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal Pelanggaran',
            'NISN',
            'Nama Siswa',
            'Kelas',
            'Kategori',
            'Jenis Pelanggaran',
            'Poin',
            'Keterangan',
            'Status',
            'Dicatat Oleh',
            'Catatan',
        ];
    }

    public function map($violation): array
    {
        return [
            $violation->id,
            $violation->violation_date->format('d/m/Y H:i'),
            $violation->student->nisn,
            $violation->student->name,
            $violation->student->class,
            $violation->violationType->category->code . ' - ' . $violation->violationType->category->name,
            $violation->violationType->name,
            $violation->points,
            $violation->description,
            strtoupper($violation->status),
            $violation->admin ? $violation->admin->name : '-',
            $violation->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
