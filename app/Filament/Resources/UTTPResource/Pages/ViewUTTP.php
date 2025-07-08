<?php

namespace App\Filament\Resources\UTTPResource\Pages;

use App\Filament\Resources\UTTPResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Support\Enums\FontWeight;

class ViewUTTP extends ViewRecord
{
    protected static string $resource = UTTPResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('warning'),
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->color('danger'),
            Actions\Action::make('view_location')
                ->label('Buka di Google Maps')
                ->icon('heroicon-o-map')
                ->color('info')
                ->url(fn () => $this->record->latitude && $this->record->longitude
                    ? "https://www.google.com/maps?q={$this->record->latitude},{$this->record->longitude}"
                    : null)
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->latitude && $this->record->longitude),
            Actions\Action::make('create_permohonan')
                ->label('Buat Permohonan Tera')
                ->icon('heroicon-o-document-plus')
                ->color('success')
                ->url(fn () => route('filament.backend.resources.permohonan-teras.create', ['uttp_id' => $this->record->id]))
                ->visible(fn () => in_array($this->record->status_tera, ['Belum Tera', 'Expired'])),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Identitas UTTP')
                    ->description('Informasi dasar dan identifikasi UTTP')
                    ->icon('heroicon-o-scale')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('kode_uttp')
                                    ->label('Kode UTTP')
                                    ->size('lg')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->copyable()
                                    ->copyMessage('Kode UTTP disalin!')
                                    ->badge(),

                                TextEntry::make('jenisUttp.nama')
                                    ->label('Jenis UTTP')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-squares-2x2'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('nomor_seri')
                                    ->label('Nomor Seri')
                                    ->copyable()
                                    ->copyMessage('Nomor seri disalin!')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('merk')
                                    ->label('Merk/Brand')
                                    ->placeholder('Tidak ada data')
                                    ->icon('heroicon-o-tag'),

                                TextEntry::make('tipe')
                                    ->label('Tipe/Model')
                                    ->placeholder('Tidak ada data')
                                    ->icon('heroicon-o-cube'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('kapasitas_maksimum')
                                    ->label('Kapasitas Maksimum')
                                    ->placeholder('Tidak ada data')
                                    ->suffix(fn ($record) => $record->jenisUttp->satuan ?? '')
                                    ->icon('heroicon-o-arrow-up-circle'),

                                TextEntry::make('daya_baca')
                                    ->label('Daya Baca')
                                    ->placeholder('Tidak ada data')
                                    ->suffix(fn ($record) => $record->jenisUttp->satuan ?? '')
                                    ->icon('heroicon-o-eye'),

                                TextEntry::make('tahun_pembuatan')
                                    ->label('Tahun Pembuatan')
                                    ->placeholder('Tidak ada data')
                                    ->icon('heroicon-o-calendar'),
                            ]),
                    ])
                    ->columns(1),

                Section::make('Data Pemilik')
                    ->description('Informasi pemilik UTTP')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('pemilik.nama')
                                    ->label('Nama Pemilik')
                                    ->size('lg')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('pemilik.nik')
                                    ->label('NIK')
                                    ->placeholder('Tidak ada data')
                                    ->copyable()
                                    ->copyMessage('NIK disalin!')
                                    ->icon('heroicon-o-identification'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('telepon_pemilik')
                                    ->label('Nomor Telepon')
                                    ->placeholder('Tidak ada data')
                                    ->copyable()
                                    ->copyMessage('Nomor telepon disalin!')
                                    ->formatStateUsing(fn ($state) => $state ? "+62 " . ltrim($state, '0') : null)
                                    ->icon('heroicon-o-phone'),

                                TextEntry::make('desa.nama_lengkap')
                                    ->label('Desa/Kecamatan')
                                    ->icon('heroicon-o-map-pin'),
                            ]),

                        TextEntry::make('alamat_pemilik')
                            ->label('Alamat Pemilik')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Lokasi UTTP')
                    ->description('Informasi lokasi dan koordinat UTTP')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('lokasi_type')
                                    ->label('Tipe Lokasi')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Pasar' => 'success',
                                        'Luar Pasar' => 'warning',
                                    })
                                    ->icon(fn (string $state): string => match ($state) {
                                        'Pasar' => 'heroicon-o-building-storefront',
                                        'Luar Pasar' => 'heroicon-o-building-office',
                                    }),

                                TextEntry::make('detail_lokasi')
                                    ->label(fn ($record) => $record->lokasi_type === 'Pasar' ? 'Lokasi Kios/Lapak' : 'Detail Lokasi')
                                    ->placeholder('Tidak ada data')
                                    ->icon('heroicon-o-map-pin'),
                            ]),

                        TextEntry::make('pasar.nama')
                            ->label('Nama Pasar')
                            ->visible(fn ($record) => $record->lokasi_type === 'Pasar')
                            ->placeholder('Tidak ada data')
                            ->icon('heroicon-o-building-storefront'),

                        TextEntry::make('alamat_lengkap')
                            ->label('Alamat Lengkap')
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('latitude')
                                    ->label('Latitude')
                                    ->placeholder('Belum diisi')
                                    ->copyable()
                                    ->copyMessage('Latitude disalin!')
                                    ->icon('heroicon-o-globe-alt'),

                                TextEntry::make('longitude')
                                    ->label('Longitude')
                                    ->placeholder('Belum diisi')
                                    ->copyable()
                                    ->copyMessage('Longitude disalin!')
                                    ->icon('heroicon-o-globe-alt'),

                                TextEntry::make('coordinates_status')
                                    ->label('Status Koordinat')
                                    ->formatStateUsing(function ($record) {
                                        return $record->latitude && $record->longitude
                                            ? 'Tersedia'
                                            : 'Belum Diisi';
                                    })
                                    ->badge()
                                    ->color(fn ($record) => $record->latitude && $record->longitude ? 'success' : 'warning')
                                    ->icon(fn ($record) => $record->latitude && $record->longitude
                                        ? 'heroicon-o-check-circle'
                                        : 'heroicon-o-exclamation-triangle'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Status Tera')
                    ->description('Informasi status dan riwayat tera')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('status_tera')
                                    ->label('Status Tera')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Aktif' => 'success',
                                        'Belum Tera' => 'warning',
                                        'Expired' => 'danger',
                                        'Rusak' => 'danger',
                                        'Tidak Layak' => 'secondary',
                                        default => 'secondary'
                                    })
                                    ->icon(fn (string $state): string => match ($state) {
                                        'Aktif' => 'heroicon-o-check-circle',
                                        'Belum Tera' => 'heroicon-o-clock',
                                        'Expired' => 'heroicon-o-exclamation-triangle',
                                        'Rusak' => 'heroicon-o-x-circle',
                                        'Tidak Layak' => 'heroicon-o-x-circle',
                                        default => 'heroicon-o-question-mark-circle'
                                    }),

                                TextEntry::make('is_active')
                                    ->label('Status UTTP')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Tidak Aktif')
                                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),

                                TextEntry::make('is_expired_soon')
                                    ->label('Status Expired')
                                    ->formatStateUsing(function ($record) {
                                        if (!$record->tanggal_expired) return 'Tidak Ada Data';
                                        return $record->is_expired_soon ? 'Akan Expired' : 'Masih Valid';
                                    })
                                    ->badge()
                                    ->color(function ($record) {
                                        if (!$record->tanggal_expired) return 'gray';
                                        return $record->is_expired_soon ? 'warning' : 'success';
                                    })
                                    ->icon(function ($record) {
                                        if (!$record->tanggal_expired) return 'heroicon-o-minus';
                                        return $record->is_expired_soon ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle';
                                    }),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('tanggal_tera_terakhir')
                                    ->label('Tanggal Tera Terakhir')
                                    ->date('d M Y')
                                    ->placeholder('Belum pernah tera')
                                    ->icon('heroicon-o-calendar'),

                                TextEntry::make('tanggal_expired')
                                    ->label('Tanggal Expired')
                                    ->date('d M Y')
                                    ->placeholder('Tidak ada data')
                                    ->icon('heroicon-o-calendar'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nomor_sertifikat')
                                    ->label('Nomor Sertifikat')
                                    ->placeholder('Tidak ada data')
                                    ->copyable()
                                    ->copyMessage('Nomor sertifikat disalin!')
                                    ->icon('heroicon-o-document-text'),

                                TextEntry::make('petugas_tera')
                                    ->label('Petugas Tera')
                                    ->placeholder('Tidak ada data')
                                    ->icon('heroicon-o-user'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Dokumentasi')
                    ->description('Foto dan keterangan UTTP')
                    ->icon('heroicon-o-camera')
                    ->schema([
                        ImageEntry::make('foto_uttp')
                            ->label('Foto UTTP')
                            ->circular()
                            ->stacked()
                            ->limit(3)
                            ->limitedRemainingText()
                            ->columnSpanFull(),

                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Tidak ada keterangan')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d M Y, H:i')
                                    ->icon('heroicon-o-calendar'),

                                TextEntry::make('updated_at')
                                    ->label('Terakhir Diperbarui')
                                    ->dateTime('d M Y, H:i')
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Section::make('Riwayat Permohonan Tera')
                    ->description('Riwayat permohonan tera untuk UTTP ini')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        TextEntry::make('permohonan_history')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                $permohonanTeras = $record->permohonanTeras()
                                    ->latest()
                                    ->take(5)
                                    ->get();

                                if ($permohonanTeras->isEmpty()) {
                                    return 'Belum ada riwayat permohonan tera';
                                }

                                return $permohonanTeras->map(function ($permohonan) {
                                    $statusIcon = match($permohonan->status) {
                                        'Pending' => '⏳',
                                        'Disetujui' => '✅',
                                        'Dijadwalkan' => '📅',
                                        'Selesai' => '✅',
                                        'Ditolak' => '❌',
                                        default => '❓'
                                    };

                                    $date = $permohonan->tanggal_permohonan->format('d M Y');
                                    $jenis = $permohonan->jenis_layanan;

                                    return "{$statusIcon} {$date} - {$jenis} ({$permohonan->status})";
                                })->join("\n");
                            })
                            ->listWithLineBreaks(),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }
}
