<?php

namespace App\Filament\Resources\HasilTeraResource\Pages;

use Filament\Actions;
use App\Models\HasilTera;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\Grid;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\HasilTeraResource;
use Filament\Infolists\Components\ImageEntry;

class ViewHasilTera extends ViewRecord
{
    protected static string $resource = HasilTeraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->visible(fn () => $this->record->created_at->diffInHours(now()) <= 24),
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->visible(fn () => $this->record->created_at->diffInHours(now()) <= 2),
            Actions\Action::make('download_certificate')
                ->label('Download Sertifikat')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn (HasilTera $record) => $record->hasil === 'Lulus' ? route('certificate.download', $record) : null)
                ->openUrlInNewTab()
                ->visible(fn (HasilTera $record) => $record->hasil === 'Lulus' && $record->nomor_sertifikat),
            Actions\Action::make('preview_certificate')
                ->label('Preview Sertifikat')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn (HasilTera $record) => $record->hasil === 'Lulus' ? route('certificate.preview', $record) : null)
                ->openUrlInNewTab()
                ->visible(fn (HasilTera $record) => $record->hasil === 'Lulus' && $record->nomor_sertifikat),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Hasil Pemeriksaan Tera')
                    ->description('Detail hasil pemeriksaan dan status tera')
                    ->icon('heroicon-o-check-badge')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('hasil')
                                    ->label('Hasil Pemeriksaan')
                                    ->size('lg')
                                    ->weight(FontWeight::Bold)
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Lulus' => 'success',
                                        'Tidak Lulus' => 'danger',
                                        'Rusak' => 'warning',
                                        'Tidak Layak' => 'secondary',
                                        default => 'gray'
                                    })
                                    ->icon(fn (string $state): string => match ($state) {
                                        'Lulus' => 'heroicon-o-check-circle',
                                        'Tidak Lulus' => 'heroicon-o-x-circle',
                                        'Rusak' => 'heroicon-o-exclamation-triangle',
                                        'Tidak Layak' => 'heroicon-o-no-symbol',
                                        default => 'heroicon-o-question-mark-circle'
                                    }),
                                    
                                TextEntry::make('nomor_sertifikat')
                                    ->label('Nomor Sertifikat')
                                    ->placeholder('Tidak ada sertifikat')
                                    ->copyable()
                                    ->copyMessage('Nomor sertifikat disalin!')
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-document-text'),
                            ]),
                            
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('tanggal_tera')
                                    ->label('Tanggal Tera')
                                    ->date('d M Y')
                                    ->icon('heroicon-o-calendar'),
                                    
                                TextEntry::make('tanggal_expired')
                                    ->label('Tanggal Expired')
                                    ->date('d M Y')
                                    ->placeholder('Tidak berlaku')
                                    ->color(function ($record) {
                                        if (!$record->tanggal_expired) return 'gray';
                                        $daysUntilExpiry = $record->tanggal_expired->diffInDays(now(), false);
                                        if ($daysUntilExpiry > 0) return 'danger';
                                        if ($daysUntilExpiry > -30) return 'warning';
                                        return 'success';
                                    })
                                    ->icon('heroicon-o-calendar-days'),
                                    
                                TextEntry::make('petugas_tera')
                                    ->label('Petugas Tera')
                                    ->icon('heroicon-o-user'),
                            ]),
                    ])
                    ->columns(1),
                    
                Section::make('Informasi Permohonan')
                    ->description('Detail permohonan tera terkait')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('permohonanTera.nomor_permohonan')
                                    ->label('Nomor Permohonan')
                                    ->copyable()
                                    ->badge()
                                    ->color('gray'),
                                    
                                TextEntry::make('permohonanTera.jenis_layanan')
                                    ->label('Jenis Layanan')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Di Kantor' => 'info',
                                        'Luar Kantor' => 'warning',
                                        'Sidang Tera' => 'success',
                                        default => 'secondary'
                                    }),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('permohonanTera.tanggal_permohonan')
                                    ->label('Tanggal Permohonan')
                                    ->date('d M Y')
                                    ->icon('heroicon-o-calendar'),
                                    
                                TextEntry::make('permohonanTera.tanggal_jadwal')
                                    ->label('Tanggal Jadwal')
                                    ->date('d M Y')
                                    ->placeholder('Tidak dijadwalkan')
                                    ->icon('heroicon-o-calendar-days'),
                            ]),
                    ])
                    ->collapsible(),
                    
                Section::make('Informasi UTTP')
                    ->description('Detail UTTP yang ditera')
                    ->icon('heroicon-o-scale')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('uttp.kode_uttp')
                                    ->label('Kode UTTP')
                                    ->badge()
                                    ->color('primary')
                                    ->copyable(),
                                    
                                TextEntry::make('uttp.nama_pemilik')
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
                                    
                                TextEntry::make('uttp.merk')
                                    ->label('Merk')
                                    ->placeholder('Tidak ada data'),
                            ]),
                            
                        TextEntry::make('uttp.lokasi_lengkap')
                            ->label('Lokasi UTTP')
                            ->icon('heroicon-o-map-pin')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                    
                Section::make('Catatan & Dokumentasi')
                    ->description('Catatan hasil dan dokumentasi foto')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('catatan_hasil')
                            ->label('Catatan Hasil Pemeriksaan')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpanFull(),
                            
                        ImageEntry::make('foto_hasil')
                            ->label('Foto Hasil Pemeriksaan')
                            ->circular()
                            ->stacked()
                            ->limit(5)
                            ->limitedRemainingText()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                    
                Section::make('Status Update Timeline')
                    ->description('Timeline update status UTTP')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        TextEntry::make('status_timeline')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                $timeline = [];
                                
                                // Permohonan dibuat
                                $timeline[] = "📝 {$record->permohonanTera->tanggal_permohonan->format('d M Y H:i')} - Permohonan tera dibuat";
                                
                                // Permohonan dijadwalkan
                                if ($record->permohonanTera->tanggal_jadwal) {
                                    $timeline[] = "📅 {$record->permohonanTera->tanggal_jadwal->format('d M Y')} - Dijadwalkan untuk tera";
                                }
                                
                                // Hasil tera diinput
                                $resultIcon = match($record->hasil) {
                                    'Lulus' => '✅',
                                    'Tidak Lulus' => '❌',
                                    'Rusak' => '⚠️',
                                    'Tidak Layak' => '🚫',
                                    default => '❓'
                                };
                                $timeline[] = "{$resultIcon} {$record->tanggal_tera->format('d M Y H:i')} - Hasil tera: {$record->hasil}";
                                
                                // Status UTTP diupdate
                                if ($record->hasil === 'Lulus') {
                                    $timeline[] = "🏆 {$record->created_at->format('d M Y H:i')} - Status UTTP diupdate ke Aktif";
                                    if ($record->nomor_sertifikat) {
                                        $timeline[] = "📜 {$record->created_at->format('d M Y H:i')} - Sertifikat {$record->nomor_sertifikat} diterbitkan";
                                    }
                                }
                                
                                return implode("\n", $timeline);
                            })
                            ->listWithLineBreaks(),
                    ])
                    ->collapsible()
                    ->collapsed(false),
                    
                Section::make('Informasi Sistem')
                    ->description('Data audit dan tracking')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d M Y, H:i')
                                    ->icon('heroicon-o-calendar'),
                                    
                                TextEntry::make('updated_at')
                                    ->label('Terakhir Diperbarui')
                                    ->dateTime('d M Y, H:i')
                                    ->icon('heroicon-o-clock'),
                                    
                                TextEntry::make('editable_until')
                                    ->label('Dapat Diedit Hingga')
                                    ->formatStateUsing(function ($record) {
                                        $editableUntil = $record->created_at->addHours(24);
                                        return $editableUntil->format('d M Y, H:i');
                                    })
                                    ->color(function ($record) {
                                        $editableUntil = $record->created_at->addHours(24);
                                        return $editableUntil->isPast() ? 'danger' : 'success';
                                    })
                                    ->icon('heroicon-o-pencil-square'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(true),
            ]);
    }
}
