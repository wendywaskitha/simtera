<?php

namespace App\Filament\Resources\DesaResource\Pages;

use App\Filament\Resources\DesaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;

class ViewDesa extends ViewRecord
{
    protected static string $resource = DesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square'),
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detail Desa')
                    ->description('Informasi lengkap desa')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nama')
                                    ->label('Nama Desa')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('primary'),
                                    
                                TextEntry::make('kecamatan.nama')
                                    ->label('Kecamatan')
                                    ->badge()
                                    ->color('primary'),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('kode')
                                    ->label('Kode Desa')
                                    ->badge()
                                    ->color('gray'),
                                    
                                TextEntry::make('nama_lengkap')
                                    ->label('Nama Lengkap')
                                    ->weight('medium'),
                            ]),
                            
                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Tidak ada keterangan')
                            ->columnSpanFull(),
                            
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('jumlah_uttp')
                                    ->label('Total UTTP')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-scale'),
                                    
                                TextEntry::make('is_active')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Tidak Aktif')
                                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                    
                                TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d M Y, H:i')
                                    ->icon('heroicon-o-calendar'),
                            ]),
                    ])
                    ->columns(1),
                    
                Section::make('Daftar UTTP')
                    ->description('UTTP yang terdaftar di desa ini')
                    ->schema([
                        TextEntry::make('uttps')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                if ($record->uttps->isEmpty()) {
                                    return 'Belum ada UTTP terdaftar';
                                }
                                
                                return $record->uttps->take(10)->map(function ($uttp) {
                                    $statusIcon = match($uttp->status_tera) {
                                        'Aktif' => '✅',
                                        'Expired' => '⚠️',
                                        'Belum Tera' => '🔄',
                                        default => '❌'
                                    };
                                    return "{$statusIcon} {$uttp->nama_pemilik} - {$uttp->jenisUttp->nama} ({$uttp->status_tera})";
                                })->join("\n") . ($record->uttps->count() > 10 ? "\n... dan " . ($record->uttps->count() - 10) . " UTTP lainnya" : '');
                            })
                            ->listWithLineBreaks(),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }
}
