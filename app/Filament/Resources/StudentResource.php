<?php

namespace App\Filament\Resources;

use App\Exports\StudentsExport;
use App\Filament\Resources\StudentResource\Pages;
use App\Models\Classroom;
use App\Models\Major;
use App\Models\Student;
use App\Services\StudentService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;  // Filament 4
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-academic-cap';
    }

    public static function getNavigationLabel(): string
    {
        return 'Data Siswa';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen Pengguna';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Data Siswa')
                    ->schema([
                        TextInput::make('nisn')
                            ->label('NISN')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        // Pilih Jurusan dulu — tidak disimpan ke DB
                        Select::make('major_id')
                            ->label('Jurusan')
                            ->options(
                                Major::where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('classroom_id', null))
                            ->dehydrated(false), // tidak disimpan ke tabel students

                        // Dropdown Kelas — difilter berdasarkan Jurusan
                        Select::make('classroom_id')
                            ->label('Kelas')
                            ->options(function (Get $get) {
                                $majorId = $get('major_id');

                                if (!$majorId) {
                                    return [];
                                }

                                return Classroom::where('major_id', $majorId)
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->disabled(fn (Get $get) => !$get('major_id'))
                            ->helperText(fn (Get $get) => !$get('major_id') ? 'Pilih jurusan terlebih dahulu' : null),

                        TextInput::make('absen')
                            ->label('Nomor Absen')
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->maxLength(255),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Akun Login')
                    ->schema([
                        TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255)
                            ->helperText('Kosongkan jika tidak ingin mengubah password'),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('classroom.major.name')
                    ->label('Jurusan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('classroom.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('absen')
                    ->label('Absen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_points')
                    ->label('Total Poin')
                    ->badge()
                    ->color(fn ($state) => $state >= 100 ? 'danger' : ($state >= 50 ? 'warning' : 'success'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('violations_count')
                    ->label('Pelanggaran')
                    ->counts('violations')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('major')
                    ->label('Jurusan')
                    ->relationship('classroom.major', 'name'),
                Tables\Filters\SelectFilter::make('classroom_id')
                    ->label('Kelas')
                    ->relationship('classroom', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('import')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        FileUpload::make('file')
                            ->label('File Excel')
                            ->acceptedFileTypes([
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $service = new StudentService();
                        $result  = $service->importFromExcel($data['file']);

                        if ($result['success']) {
                            \Filament\Notifications\Notification::make()
                                ->title('Berhasil!')
                                ->body($result['message'])
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal!')
                                ->body($result['message'])
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => Excel::download(
                        new StudentsExport(),
                        'siswa-' . now()->format('Y-m-d') . '.xlsx'
                    )),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit'   => Pages\EditStudent::route('/{record}/edit'),
            'view'   => Pages\ViewStudent::route('/{record}'),
        ];
    }
}
