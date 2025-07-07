<?php

namespace App\Filament\Resources\PasarResource\Pages;

use App\Filament\Resources\PasarResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Actions\Action;
use Filament\Support\Enums\FontWeight;

class ViewPasar extends ViewRecord
{
    protected static string $resource = PasarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('warning'),
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->color('danger'),
            Actions\Action::make('view_map')
                ->label('Buka di Google Maps')
                ->icon('heroicon-o-map')
                ->color('info')
                ->url(fn () => $this->record->latitude && $this->record->longitude 
                    ? "https://www.google.com/maps?q={$this->record->latitude},{$this->record->longitude}" 
                    : null)
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->latitude && $this->record->longitude),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Pasar')
                    ->description('Detail lengkap informasi pasar')
                    ->icon('heroicon-o-building-storefront')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nama')
                                    ->label('Nama Pasar')
                                    ->size('lg')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->icon('heroicon-o-building-storefront'),
                                    
                                TextEntry::make('alamat_lengkap')
                                    ->label('Alamat Lengkap')
                                    ->weight(FontWeight::Medium)
                                    ->icon('heroicon-o-map-pin'),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('desa.nama')
                                    ->label('Desa/Kelurahan')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-building-office'),
                                    
                                TextEntry::make('desa.kecamatan.nama')
                                    ->label('Kecamatan')
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-map'),
                            ]),
                            
                        TextEntry::make('alamat')
                            ->label('Alamat Detail')
                            ->placeholder('Tidak ada alamat detail')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
                    
                Section::make('Koordinat & Lokasi')
                    ->description('Informasi geografis pasar')
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('latitude')
                                    ->label('Latitude')
                                    ->placeholder('Belum diisi')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-globe-alt'),
                                    
                                TextEntry::make('longitude')
                                    ->label('Longitude')
                                    ->placeholder('Belum diisi')
                                    ->badge()
                                    ->color('success')
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
                    
                Section::make('Kontak & Pengelola')
                    ->description('Informasi kontak pasar')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('kontak_person')
                                    ->label('Nama Pengelola')
                                    ->placeholder('Tidak ada data')
                                    ->icon('heroicon-o-user')
                                    ->weight(FontWeight::Medium),
                                    
                                TextEntry::make('telepon')
                                    ->label('Nomor Telepon')
                                    ->placeholder('Tidak ada data')
                                    ->icon('heroicon-o-phone')
                                    ->copyable()
                                    ->copyMessage('Nomor telepon disalin!')
                                    ->formatStateUsing(fn ($state) => $state ? "+62 " . ltrim($state, '0') : null),
                            ]),
                            
                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Tidak ada keterangan tambahan')
                            ->columnSpanFull(),
                            
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('is_active')
                                    ->label('Status Pasar')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Tidak Aktif')
                                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                    
                                TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d M Y, H:i')
                                    ->icon('heroicon-o-calendar'),
                                    
                                TextEntry::make('updated_at')
                                    ->label('Diperbarui')
                                    ->dateTime('d M Y, H:i')
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->collapsible(),
                    
                Section::make('Statistik UTTP')
                    ->description('Data UTTP yang terdaftar di pasar ini')
                    ->icon('heroicon-o-scale')
                    ->schema([
                        TextEntry::make('uttps_summary')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                if ($record->uttps->isEmpty()) {
                                    return 'Belum ada UTTP terdaftar di pasar ini';
                                }
                                
                                $statusCounts = $record->uttps->groupBy('status_tera')->map->count();
                                $result = [];
                                
                                foreach ($statusCounts as $status => $count) {
                                    $icon = match($status) {
                                        'Aktif' => '✅',
                                        'Expired' => '⚠️',
                                        'Belum Tera' => '🔄',
                                        'Rusak' => '❌',
                                        'Tidak Layak' => '🚫',
                                        default => '❓'
                                    };
                                    $result[] = "{$icon} {$status}: {$count} unit";
                                }
                                
                                $result[] = "\n📊 Total UTTP: " . $record->uttps->count() . " unit";
                                
                                return implode("\n", $result);
                            })
                            ->listWithLineBreaks(),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }
}
