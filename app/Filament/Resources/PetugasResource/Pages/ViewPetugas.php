<?php

namespace App\Filament\Resources\PetugasResource\Pages;

use App\Filament\Resources\PetugasResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Support\Enums\FontWeight;

class ViewPetugas extends ViewRecord
{
    protected static string $resource = PetugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('warning'),
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->color('danger'),
            Actions\Action::make('send_email')
                ->label('Kirim Email')
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->url(fn () => $this->record->email ? "mailto:{$this->record->email}" : null)
                ->visible(fn () => !is_null($this->record->email)),
            Actions\Action::make('call_phone')
                ->label('Telepon')
                ->icon('heroicon-o-phone')
                ->color('success')
                ->url(fn () => $this->record->telepon ? "tel:{$this->record->telepon}" : null)
                ->visible(fn () => !is_null($this->record->telepon)),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Petugas')
                    ->description('Detail lengkap informasi petugas')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nama')
                                    ->label('Nama Lengkap')
                                    ->size('lg')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->icon('heroicon-o-user'),
                                    
                                TextEntry::make('nip')
                                    ->label('NIP')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable()
                                    ->copyMessage('NIP disalin!')
                                    ->icon('heroicon-o-identification'),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('jabatan')
                                    ->label('Jabatan')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-briefcase'),
                                    
                                TextEntry::make('is_active')
                                    ->label('Status Petugas')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Tidak Aktif')
                                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                            ]),
                    ])
                    ->columns(1),
                    
                Section::make('Kontak & Alamat')
                    ->description('Informasi kontak petugas')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('telepon')
                                    ->label('Nomor Telepon')
                                    ->placeholder('Tidak ada data')
                                    ->icon('heroicon-o-phone')
                                    ->copyable()
                                    ->copyMessage('Nomor telepon disalin!')
                                    ->formatStateUsing(fn ($state) => $state ? "+62 " . ltrim($state, '0') : null),
                                    
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->placeholder('Tidak ada data')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable()
                                    ->copyMessage('Email disalin!'),
                            ]),
                            
                        TextEntry::make('alamat')
                            ->label('Alamat Lengkap')
                            ->placeholder('Tidak ada alamat')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                    
                Section::make('Kompetensi & Keahlian')
                    ->description('Jenis UTTP yang dapat ditangani')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        TextEntry::make('kompetensi')
                            ->label('Kompetensi UTTP')
                            ->formatStateUsing(function ($record) {
                                if (!$record->kompetensi || !is_array($record->kompetensi)) {
                                    return 'Belum ada kompetensi yang ditetapkan';
                                }
                                
                                return collect($record->kompetensi)->map(function ($item) {
                                    $icon = match($item) {
                                        'Timbangan Digital' => '⚖️',
                                        'Timbangan Mekanik' => '⚖️',
                                        'Takaran BBM' => '⛽',
                                        'Takaran LPG' => '🔥',
                                        'Meter Kain' => '📏',
                                        'Anak Timbangan' => '🏋️',
                                        'Administrasi' => '📋',
                                        'Koordinasi' => '🤝',
                                        'Supervisi' => '👁️',
                                        default => '✅'
                                    };
                                    return "{$icon} {$item}";
                                })->join("\n");
                            })
                            ->listWithLineBreaks(),
                    ])
                    ->collapsible(),
                    
                Section::make('Statistik Kinerja')
                    ->description('Data kinerja dan beban kerja petugas')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('jumlah_tugas_aktif')
                                    ->label('Tugas Aktif')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-clipboard-document-list'),
                                    
                                TextEntry::make('total_tera_selesai')
                                    ->label('Total Tera Selesai')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-check-circle'),
                                    
                                TextEntry::make('created_at')
                                    ->label('Bergabung Sejak')
                                    ->dateTime('d M Y')
                                    ->icon('heroicon-o-calendar'),
                                    
                                TextEntry::make('updated_at')
                                    ->label('Terakhir Diperbarui')
                                    ->dateTime('d M Y, H:i')
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->collapsible(),
                    
                Section::make('Riwayat Tugas Terbaru')
                    ->description('10 tugas tera terbaru yang ditangani')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        TextEntry::make('recent_tasks')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                $recentTasks = $record->permohonanTeras()
                                    ->with(['uttp.jenisUttp', 'uttp.desa'])
                                    ->latest()
                                    ->take(10)
                                    ->get();
                                
                                if ($recentTasks->isEmpty()) {
                                    return 'Belum ada tugas yang ditangani';
                                }
                                
                                return $recentTasks->map(function ($task) {
                                    $statusIcon = match($task->status) {
                                        'Pending' => '⏳',
                                        'Disetujui' => '✅',
                                        'Dijadwalkan' => '📅',
                                        'Selesai' => '✅',
                                        'Ditolak' => '❌',
                                        default => '❓'
                                    };
                                    
                                    $date = $task->tanggal_permohonan->format('d M Y');
                                    $uttp = $task->uttp->jenisUttp->nama;
                                    $lokasi = $task->uttp->desa->nama;
                                    
                                    return "{$statusIcon} {$date} - {$uttp} di {$lokasi} ({$task->status})";
                                })->join("\n");
                            })
                            ->listWithLineBreaks(),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }
}
