<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ViolationResource\Pages;
use App\Models\Violation;
use App\Models\Student;
use App\Models\ViolationType;
use App\Services\ViolationService;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ViolationsExport;

class ViolationResource extends Resource
{
    protected static ?string $model = Violation::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-exclamation-triangle';
    }

    public static function getNavigationLabel(): string
    {
        return 'Data Pelanggaran';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Pengelolaan Pelanggaran';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Data Pelanggaran')
                    ->schema([
                        Select::make('student_id')
                            ->label('Siswa')
                            ->options(function () {
                                return Student::where('is_active', true)
                                    ->get()
                                    ->mapWithKeys(function ($student) {
                                        return [$student->id => "{$student->name} - {$student->nisn} ({$student->class})"];
                                    });
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),

                        Select::make('violation_type_id')
                            ->label('Jenis Pelanggaran')
                            ->options(function () {
                                return ViolationType::with('category')
                                    ->get()
                                    ->mapWithKeys(function ($type) {
                                        return [$type->id => "{$type->category->code} - {$type->name} ({$type->points} poin)"];
                                    });
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
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
                            ->disabled(function (callable $get) {
                                $typeId = $get('violation_type_id');
                                if (!$typeId) {
                                    return true;
                                }
                                $type = ViolationType::find($typeId);
                                return $type ? !$type->is_custom : true;
                            })
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('violation_date')
                    ->label('Tanggal')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.class')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('violationType.category.code')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('violationType.name')
                    ->label('Jenis Pelanggaran')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('points')
                    ->label('Poin')
                    ->badge()
                    ->color(function ($state) {
                        if ($state >= 50) return 'danger';
                        if ($state >= 25) return 'warning';
                        return 'success';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(function (string $state): string {
                        return match ($state) {
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'pending' => 'Pending',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                            default => $state,
                        };
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('admin.name')
                    ->label('Dicatat Oleh')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),

                Tables\Filters\SelectFilter::make('violation_category')
                    ->label('Kategori')
                    ->relationship('violationType.category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('violation_date')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('date_to')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn ($q) => $q->whereDate('violation_date', '>=', $data['date_from'])
                            )
                            ->when(
                                $data['date_to'],
                                fn ($q) => $q->whereDate('violation_date', '<=', $data['date_to'])
                            );
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(function (Violation $record): bool {
                        return $record->status === 'pending';
                    })
                    ->action(function (Violation $record) {
                        $service = new ViolationService();
                        $service->approveViolation($record);
                        \Filament\Notifications\Notification::make()
                            ->title('Pelanggaran Disetujui')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(function (Violation $record): bool {
                        return $record->status === 'pending';
                    })
                    ->form([
                        Textarea::make('notes')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(function (Violation $record, array $data) {
                        $service = new ViolationService();
                        $service->rejectViolation($record, $data['notes']);
                        \Filament\Notifications\Notification::make()
                            ->title('Pelanggaran Ditolak')
                            ->success()
                            ->send();
                    }),

                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->placeholder('Semua Status'),

                        DatePicker::make('date_from')
                            ->label('Dari Tanggal'),

                        DatePicker::make('date_to')
                            ->label('Sampai Tanggal'),
                    ])
                    ->action(function (array $data) {
                        return Excel::download(
                            new ViolationsExport($data),
                            'pelanggaran-' . now()->format('Y-m-d') . '.xlsx'
                        );
                    }),
            ])
            ->defaultSort('violation_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListViolations::route('/'),
            'create' => Pages\CreateViolation::route('/create'),
            'edit' => Pages\EditViolation::route('/{record}/edit'),
            'view' => Pages\ViewViolation::route('/{record}'),
        ];
    }
}
