<?php

namespace App\Filament\Resources\Violations\Schemas;

use App\Models\Student;
use App\Models\ViolationType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ViolationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pelanggaran')
                    ->schema([
                        Select::make('student_id')
                            ->label('Siswa')
                            ->options(Student::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - {$record->nisn} ({$record->class})")
                            ->columnSpanFull(),

                        Select::make('violation_type_id')
                            ->label('Jenis Pelanggaran')
                            ->options(ViolationType::with('category')->get()->mapWithKeys(function ($type) {
                                return [$type->id => "{$type->category->code} - {$type->name} ({$type->points} poin)"];
                            }))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $violationType = ViolationType::find($state);
                                if ($violationType && !$violationType->is_custom) {
                                    $set('points', $violationType->points);
                                }
                            })
                            ->columnSpanFull(),

                        DateTimePicker::make('violation_date')
                            ->label('Tanggal & Waktu Pelanggaran')
                            ->required()
                            ->default(now())
                            ->native(false),

                        TextInput::make('points')
                            ->label('Poin')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->disabled(fn (callable $get) => !$get('violation_type_id') || !ViolationType::find($get('violation_type_id'))?->is_custom)
                            ->helperText('Poin otomatis terisi. Hanya dapat diubah jika jenis pelanggaran mengizinkan custom poin'),

                        Textarea::make('description')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::make('photo_evidence')
                            ->label('Bukti Foto')
                            ->image()
                            ->directory('violations/photos')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->columnSpanFull(),

                        FileUpload::make('signature')
                            ->label('Tanda Tangan')
                            ->image()
                            ->directory('violations/signatures')
                            ->maxSize(1024)
                            ->columnSpanFull(),

                        TextInput::make('admin_id')
                            ->numeric()
                            ->hidden()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                Section::make('Status & Catatan')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->required()
                            ->default('pending'),

                        Textarea::make('notes')
                            ->label('Catatan Admin')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
