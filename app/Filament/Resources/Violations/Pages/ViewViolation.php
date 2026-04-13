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
                                'pending'  => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default    => 'gray',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'pending'  => 'Pending',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                default    => $state,
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
        $id = 'zoom-' . md5($src . $label);

        return <<<HTML
        <div
            id="{$id}-modal"
            onclick="zoomModalClose('{$id}')"
            style="
                display: none;
                position: fixed;
                inset: 0;
                z-index: 99999;
                background: rgba(0,0,0,0.85);
                cursor: zoom-out;
                align-items: center;
                justify-content: center;
            "
        >
            <div onclick="event.stopPropagation()" style="position: relative; display: flex; flex-direction: column; align-items: center; gap: 12px;">
                <button
                    onclick="zoomModalClose('{$id}')"
                    style="
                        position: absolute; top: -40px; right: 0;
                        background: rgba(255,255,255,0.15); border: none;
                        color: white; font-size: 24px;
                        width: 36px; height: 36px;
                        border-radius: 50%; cursor: pointer; line-height: 1;
                    "
                >&times;</button>

                <img
                    id="{$id}-zoomimg"
                    src="{$src}"
                    alt="{$label}"
                    style="
                        max-width: 90vw; max-height: 80vh;
                        border-radius: 8px;
                        box-shadow: 0 8px 32px rgba(0,0,0,0.5);
                        transform-origin: center center;
                        transform: scale(1);
                        transition: transform 0.2s ease;
                        cursor: grab; display: block; background: white;
                    "
                />

                <div style="display: flex; gap: 8px; align-items: center;">
                    <button onclick="zoomChange('{$id}', -0.25)" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4); color: white; font-size: 20px; width: 40px; height: 40px; border-radius: 8px; cursor: pointer;">&#8722;</button>
                    <span id="{$id}-label" style="color: white; font-size: 14px; min-width: 50px; text-align: center;">100%</span>
                    <button onclick="zoomChange('{$id}', 0.25)" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4); color: white; font-size: 20px; width: 40px; height: 40px; border-radius: 8px; cursor: pointer;">&#43;</button>
                    <button onclick="zoomReset('{$id}')" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4); color: white; font-size: 12px; padding: 0 12px; height: 40px; border-radius: 8px; cursor: pointer;">Reset</button>
                </div>

                <p style="color: rgba(255,255,255,0.5); font-size: 12px; margin: 0;">
                    Scroll untuk zoom &bull; Klik luar gambar untuk tutup
                </p>
            </div>
        </div>

        <div
            onclick="zoomModalOpen('{$id}')"
            style="cursor: zoom-in; display: inline-block; border: 2px solid #e5e7eb; border-radius: 8px; overflow: hidden; transition: box-shadow 0.2s; max-width: 100%;"
            onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.15)'"
            onmouseout="this.style.boxShadow='none'"
            title="Klik untuk zoom"
        >
            <img src="{$src}" alt="{$label}" style="max-width: 500px; width: 100%; height: auto; display: block;" />
            <div style="background: rgba(0,0,0,0.05); text-align: center; font-size: 12px; color: #6b7280; padding: 4px;">
                🔍 Klik untuk perbesar
            </div>
        </div>

        <script>
        (function() {
            if (!window._zoomLevels) window._zoomLevels = {};

            window.zoomModalOpen = function(id) {
                var modal = document.getElementById(id + '-modal');
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                window._zoomLevels[id] = 1;
                _zoomApply(id);
                var img = document.getElementById(id + '-zoomimg');
                img._wheelHandler = function(e) {
                    e.preventDefault();
                    zoomChange(id, e.deltaY > 0 ? -0.15 : 0.15);
                };
                img.addEventListener('wheel', img._wheelHandler, { passive: false });
            };

            window.zoomModalClose = function(id) {
                var modal = document.getElementById(id + '-modal');
                modal.style.display = 'none';
                document.body.style.overflow = '';
                var img = document.getElementById(id + '-zoomimg');
                if (img && img._wheelHandler) img.removeEventListener('wheel', img._wheelHandler);
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
                document.querySelectorAll('[id\$="-modal"]').forEach(function(m) {
                    if (m.style.display === 'flex') zoomModalClose(m.id.replace('-modal',''));
                });
            });
        })();
        </script>
        HTML;
    }
}
