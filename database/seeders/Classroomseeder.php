<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Major;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        $structure = [
            'RPL' => [
                'X'   => ['X RPL 1', 'X RPL 2', 'X RPL 3'],
                'XI'  => ['XI RPL 1', 'XI RPL 2', 'XI RPL 3'],
                'XII' => ['XII RPL 1', 'XII RPL 2', 'XII RPL 3'],
            ],
            'DKV' => [
                'X'   => ['X DKV 1', 'X DKV 2', 'X DKV 3'],
                'XI'  => ['XI DKV 1', 'XI DKV 2', 'XI DKV 3'],
                'XII' => ['XII DKV 1', 'XII DKV 2', 'XII DKV 3'],
            ],
            'LSP' => [
                'X'   => ['X LSP 1', 'X LSP 2', 'X LSP 3'],
                'XI'  => ['XI LSP 1', 'XI LSP 2', 'XI LSP 3'],
                'XII' => ['XII LSP 1', 'XII LSP 2', 'XII LSP 3'],
            ],
            'APHP' => [
                'X'   => ['X APHP 1', 'X APHP 2', 'X APHP 3'],
                'XI'  => ['XI APHP 1', 'XI APHP 2', 'XI APHP 3'],
                'XII' => ['XII APHP 1', 'XII APHP 2', 'XII APHP 3'],
            ],
            'Kuliner' => [
                'X'   => ['X Kuliner 1', 'X Kuliner 2', 'X Kuliner 3'],
                'XI'  => ['XI Kuliner 1', 'XI Kuliner 2', 'XI Kuliner 3'],
                'XII' => ['XII Kuliner 1', 'XII Kuliner 2', 'XII Kuliner 3'],
            ],
        ];

        foreach ($structure as $code => $grades) {
            $major = Major::where('code', $code)->first();

            if (!$major) continue;

            foreach ($grades as $grade => $classNames) {
                foreach ($classNames as $name) {
                    Classroom::firstOrCreate(
                        ['name' => $name, 'major_id' => $major->id],
                        [
                            'grade'     => $grade,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
