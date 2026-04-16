<?php

namespace App\Filament\Resources\ViolationResource\Pages;

use App\Filament\Resources\ViolationResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ViewViolation extends ViewRecord
{
    protected static string $resource = ViolationResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Inject JavaScript zoom sekali saja saat halaman dimount
        $this->js("
            if (!window._zoomReady) {
                window._zoomReady = true;
                window._zoomLevels = {};

                window.zoomModalOpen = function(id) {
                    var modal = document.getElementById(id + '-modal');
                    if (!modal) return;
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                    window._zoomLevels[id] = 1;
                    _zoomApply(id);
                    var img = document.getElementById(id + '-zoomimg');
                    if (img) {
                        img._wheelHandler = function(e) {
                            e.preventDefault();
                            zoomChange(id, e.deltaY > 0 ? -0.15 : 0.15);
                        };
                        img.addEventListener('wheel', img._wheelHandler, { passive: false });
                    }
                };

                window.zoomModalClose = function(id) {
                    var modal = document.getElementById(id + '-modal');
                    if (!modal) return;
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                    var img = document.getElementById(id + '-zoomimg');
                    if (img && img._wheelHandler) {
                        img.removeEventListener('wheel', img._wheelHandler);
                    }
                };

                window.zoomChange = function(id, delta) {
                    window._zoomLevels[id] = Math.min(Math.max((window._zoomLevels[id] || 1) + delta, 0.5), 5);
                    _zoomApply(id);
                };

                window.zoomReset = function(id) {
                    window._zoomLevels[id] = 1;
                    _zoomApply(id);
                };

                function _zoomApply(id) {
                    var scale = window._zoomLevels[id] || 1;
                    var img = document.getElementById(id + '-zoomimg');
                    var lbl = document.getElementById(id + '-label');
                    if (img) img.style.transform = 'scale(' + scale + ')';
                    if (lbl) lbl.textContent = Math.round(scale * 100) + '%';
                }

                document.addEventListener('keydown', function(e) {
                    if (e.key !== 'Escape') return;
                    document.querySelectorAll('[id\$=\"-modal\"]').forEach(function(m) {
                        if (m.style.display === 'flex') {
                            zoomModalClose(m.id.replace('-modal', ''));
                        }
                    });
                });
            }
        ");
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Data Siswa')
                    ->schema([
                        TextEntry::make('student.nisn')
                            ->label('NISN'),
                        TextEntry::make('student.name')
                            ->label('Nama'),
                        TextEntry::make('student.class')
                            ->label('Kelas'),
                    ])
                    ->columns(3),

                Section::make('Data Pelanggaran')
                    ->schema([
                        TextEntry::make('violationType.category.name')
                            ->label('Kategori')
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('violationType.name')
                            ->label('Jenis Pelanggaran'),
                        TextEntry::make('points')
                            ->label('Poin')
                            ->badge()
                            ->color(fn($state) => $state >= 50 ? 'danger' : ($state >= 25 ? 'warning' : 'success')),
                        TextEntry::make('violation_date')
                            ->label('Tanggal Pelanggaran')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('description')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Bukti')
                    ->schema([
                        TextEntry::make('photo_evidence')
                            ->label('Foto Bukti')
                            ->html()
                            ->formatStateUsing(function ($state) {
                                $url = asset('storage/' . $state);
                                return self::zoomableImageHtml($url, 'Foto Bukti');
                            })
                            ->columnSpanFull()
                            ->visible(fn($record) => $record->photo_evidence),
                    ])
                    ->visible(fn($record) => $record->photo_evidence),

                Section::make('Tanda Tangan')
                    ->schema([
                        // Tanda tangan dari SignaturePad (base64)
                        TextEntry::make('signature')
                            ->label('Tanda Tangan')
                            ->html()
                            ->formatStateUsing(function ($state) {
                                return self::zoomableImageHtml($state, 'Tanda Tangan');
                            })
                            ->columnSpanFull()
                            ->visible(fn($record) => $record->signature && str_starts_with($record->signature, 'data:image')),

                        // Tanda tangan dari FileUpload (path file)
                        TextEntry::make('signature')
                            ->label('Tanda Tangan')
                            ->html()
                            ->formatStateUsing(function ($state) {
                                $url = asset('storage/' . $state);
                                return self::zoomableImageHtml($url, 'Tanda Tangan');
                            })
                            ->columnSpanFull()
                            ->visible(fn($record) => $record->signature && !str_starts_with($record->signature, 'data:image')),
                    ])
                    ->visible(fn($record) => $record->signature),

                Section::make('Status')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'pending' => 'Pending',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                default => $state,
                            }),
                        TextEntry::make('admin.name')
                            ->label('Dicatat Oleh'),
                        TextEntry::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull()
                            ->visible(fn($record) => $record->notes),
                    ])
                    ->columns(2),
            ]);
    }


private static function zoomableImageHtml(string $src, string $label = 'Gambar'): string
{
    return view('components.zoomable-image', [
        'src'   => $src,
        'label' => $label,
    ])->render();
}
}
