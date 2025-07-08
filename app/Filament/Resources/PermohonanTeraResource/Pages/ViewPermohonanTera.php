<?php

namespace App\Filament\Resources\PermohonanTeraResource\Pages;

use App\Filament\Resources\PermohonanTeraResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Support\Enums\FontWeight;

class ViewPermohonanTera extends ViewRecord
{
    protected static string $resource = PermohonanTeraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->visible(fn () => in_array($this->record->status, ['Pending', 'Disetujui'])),
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->visible(fn () => $this->record->status === 'Pending'),
            Actions\Action::make('approve')
                ->label('Setujui Permohonan')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => 'Disetujui']);
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(fn () => $this->record->status === 'Pending'),
            Actions\Action::make('download_dokumen')
                ->label('Download Dokumen')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->url(fn () => $this->record->dokumen_pendukung ? route('download.dokumen', $this->record) : null)
                ->openUrlInNewTab()
                ->visible(fn () => !empty($this->record->dokumen_pendukung)),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Status Timeline')
                    ->description('Timeline status permohonan tera')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        TextEntry::make('status_timeline')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                $timeline = [
                                    'Pending' => ['icon' => '⏳', 'color' => 'warning', 'desc' => 'Menunggu persetujuan'],
                                    'Disetujui' => ['icon' => '✅', 'color' => 'success', 'desc' => 'Permohonan disetujui'],
                                    'Dijadwalkan' => ['icon' => '📅', 'color' => 'info', 'desc' => 'Sudah dijadwalkan'],
                                    'Selesai' => ['icon' => '🎉', 'color' => 'success', 'desc' => 'Tera selesai'],
                                    'Ditolak' => ['icon' => '❌', 'color' => 'danger', 'desc' => 'Permohonan ditolak'],
                                ];

                                $currentStatus = $record->status;
                                $result = [];

                                foreach ($timeline as $status => $info) {
                                    $isActive = $status === $currentStatus;
                                    $isPassed = array_search($status, array_keys($timeline)) < array_search($currentStatus, array_keys($timeline));

                                    if ($isActive) {
                                        $result[] = "🔄 **{$status}** - {$info['desc']} (Status Saat Ini)";
                                    } elseif ($isPassed) {
                                        $result[] = "✅ {$status} - {$info['desc']}";
                                    } else {
                                        $result[] = "⚪ {$status} - {$info['desc']}";
                                    }
                                }

                                return implode("\n", $result);
                            })
                            ->listWithLineBreaks(),
                    ])
                    ->collapsible(),

                Section::make('Informasi Permohonan')
                    ->description('Detail permohonan tera')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nomor_permohonan')
                                    ->label('Nomor Permohonan')
                                    ->size('lg')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->copyable()
                                    ->copyMessage('Nomor permohonan disalin!')
                                    ->badge(),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Pending' => 'warning',
                                        'Disetujui' => 'info',
                                        'Dijadwalkan' => 'primary',
                                        'Selesai' => 'success',
                                        'Ditolak' => 'danger',
                                        default => 'secondary'
                                    })
                                    ->icon(fn (string $state): string => match ($state) {
                                        'Pending' => 'heroicon-o-clock',
                                        'Disetujui' => 'heroicon-o-check-circle',
                                        'Dijadwalkan' => 'heroicon-o-calendar',
                                        'Selesai' => 'heroicon-o-check-badge',
                                        'Ditolak' => 'heroicon-o-x-circle',
                                        default => 'heroicon-o-question-mark-circle'
                                    }),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('jenis_layanan')
                                    ->label('Jenis Layanan')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Di Kantor' => 'info',
                                        'Luar Kantor' => 'warning',
                                        'Sidang Tera' => 'success',
                                        default => 'secondary'
                                    })
                                    ->icon(fn (string $state): string => match ($state) {
                                        'Di Kantor' => 'heroicon-o-building-office',
                                        'Luar Kantor' => 'heroicon-o-truck',
                                        'Sidang Tera' => 'heroicon-o-building-storefront',
                                        default => 'heroicon-o-cog-6-tooth'
                                    }),

                                TextEntry::make('petugas_assigned')
                                    ->label('Petugas Ditugaskan')
                                    ->placeholder('Belum ditugaskan')
                                    ->icon('heroicon-o-user'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('tanggal_permohonan')
                                    ->label('Tanggal Permohonan')
                                    ->date('d M Y')
                                    ->icon('heroicon-o-calendar'),

                                TextEntry::make('tanggal_jadwal')
                                    ->label('Tanggal Jadwal')
                                    ->date('d M Y')
                                    ->placeholder('Belum dijadwalkan')
                                    ->icon('heroicon-o-calendar-days'),
                            ]),
                    ])
                    ->columns(1),

                Section::make('Informasi UTTP')
                    ->description('Detail UTTP yang akan ditera')
                    ->icon('heroicon-o-scale')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('uttp.kode_uttp')
                                    ->label('Kode UTTP')
                                    ->badge()
                                    ->color('primary')
                                    ->copyable(),

                                TextEntry::make('uttp.pemilik.nama')
                                    ->label('Nama Pemilik')
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-o-user'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('uttp.jenisUttp.nama')
                                    ->label('Jenis UTTP')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('uttp.nomor_seri')
                                    ->label('Nomor Seri')
                                    ->copyable(),

                                TextEntry::make('uttp.status_tera')
                                    ->label('Status Tera UTTP')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Aktif' => 'success',
                                        'Belum Tera' => 'warning',
                                        'Expired' => 'danger',
                                        default => 'secondary'
                                    }),
                            ]),

                        TextEntry::make('uttp.lokasi_lengkap')
                            ->label('Lokasi UTTP')
                            ->icon('heroicon-o-map-pin')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Catatan & Dokumentasi')
                    ->description('Catatan dan dokumen pendukung')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('catatan_pemohon')
                            ->label('Catatan Pemohon')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpanFull(),

                        TextEntry::make('catatan_petugas')
                            ->label('Catatan Petugas')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpanFull(),

                        TextEntry::make('dokumen_pendukung')
                            ->label('Dokumen Pendukung')
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) {
                                    return 'Tidak ada dokumen';
                                }
                                return is_array($state) ? count($state) . ' file' : '1 file';
                            })
                            ->badge()
                            ->color(fn ($state) => empty($state) ? 'gray' : 'success')
                            ->icon(fn ($state) => empty($state) ? 'heroicon-o-document' : 'heroicon-o-document-check'),
                    ])
                    ->collapsible(),

                Section::make('Riwayat Aktivitas')
                    ->description('Log aktivitas permohonan')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        TextEntry::make('activity_log')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                $activities = [
                                    $record->created_at->format('d M Y H:i') . ' - Permohonan dibuat',
                                ];

                                if ($record->status !== 'Pending') {
                                    $activities[] = $record->updated_at->format('d M Y H:i') . ' - Status diubah ke ' . $record->status;
                                }

                                if ($record->petugas_assigned) {
                                    $activities[] = $record->updated_at->format('d M Y H:i') . ' - Ditugaskan ke ' . $record->petugas_assigned;
                                }

                                return implode("\n", $activities);
                            })
                            ->listWithLineBreaks(),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }
}
