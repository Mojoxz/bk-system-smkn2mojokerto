<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ViolationResource\Pages;
use App\Models\Violation;
use App\Models\Student;
use App\Models\ViolationType;
use App\Services\ViolationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ViolationsExport;

class ViolationResource extends Resource
{
    protected static ?string $model = Violation::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationLabel = 'Data Pelanggaran';

    protected static ?string $navigationGroup = 'Pengelolaan Pelanggaran';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pelanggaran')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Siswa')
                            ->options(Student::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - {$record->nisn} ({$record->class})")
                            ->columnSpanFull(),

                        Forms\Components\Select::make('violation_type_id')
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

                        Forms\Components\DateTimePicker::make('violation_date')
                            ->label('Tanggal & Waktu Pelanggaran')
                            ->required()
                            ->default(now())
                            ->native(false),

                        Forms\Components\TextInput::make('points')
                            ->label('Poin')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->disabled(fn (callable $get) => !$get('violation_type_id') || !ViolationType::find($get('violation_type_id'))?->is_custom)
                            ->helperText('Poin otomatis terisi. Hanya dapat diubah jika jenis pelanggaran mengizinkan custom poin'),

                        Forms\Components\Textarea::make('description')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('photo_evidence')
                            ->label('Bukti Foto')
                            ->image()
                            ->directory('violations/photos')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('signature')
                            ->label('Tanda Tangan')
                            ->image()
                            ->directory('violations/signatures')
                            ->maxSize(1024)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status & Catatan')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->required()
                            ->default('pending'),

                        Forms\Components\Textarea::make('notes')
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
                    ->color(fn ($state) => $state >= 50 ? 'danger' : ($state >= 25 ? 'warning' : 'success'))
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
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
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['date_from'], fn ($q) => $q->whereDate('violation_date', '>=', $data['date_from']))
                            ->when($data['date_to'], fn ($q) => $q->whereDate('violation_date', '<=', $data['date_to']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Violation $record) => $record->status === 'pending')
                    ->action(function (Violation $record, ViolationService $service) {
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
                    ->visible(fn (Violation $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(function (Violation $record, array $data, ViolationService $service) {
                        $service->rejectViolation($record, $data['notes']);
                        \Filament\Notifications\Notification::make()
                            ->title('Pelanggaran Ditolak')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->placeholder('Semua Status'),

                        Forms\Components\DatePicker::make('date_from')
                            ->label('Dari Tanggal'),

                        Forms\Components\DatePicker::make('date_to')
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
