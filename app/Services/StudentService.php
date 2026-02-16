<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;

class StudentService
{
    public function createStudent(array $data): Student
    {
        $data['password'] = Hash::make($data['password'] ?? $data['nisn']);

        if (!isset($data['username'])) {
            $data['username'] = $data['nisn'];
        }

        return Student::create($data);
    }

    public function updateStudent(Student $student, array $data): Student
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $student->update($data);

        return $student;
    }

    public function deleteStudent(Student $student): bool
    {
        return $student->delete();
    }

    public function importFromExcel($file): array
    {
        DB::beginTransaction();
        try {
            $import = new StudentsImport();
            Excel::import($import, $file);

            DB::commit();

            return [
                'success' => true,
                'imported' => $import->getRowCount(),
                'message' => "Berhasil mengimport {$import->getRowCount()} siswa"
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function getStudentStatistics(Student $student): array
    {
        return [
            'total_violations' => $student->violations()->count(),
            'pending_violations' => $student->violations()->where('status', 'pending')->count(),
            'approved_violations' => $student->violations()->where('status', 'approved')->count(),
            'total_points' => $student->total_points,
            'violations_by_category' => $student->violations()
                ->with('violationType.category')
                ->where('status', 'approved')
                ->get()
                ->groupBy('violationType.category.name')
                ->map(fn($violations) => [
                    'count' => $violations->count(),
                    'points' => $violations->sum('points')
                ])
                ->toArray()
        ];
    }
}
