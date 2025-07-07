<?php

namespace App\Filament\Resources\KecamatanResource\Pages;

use App\Filament\Resources\KecamatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;

class ViewKecamatan extends ViewRecord
{
    protected static string $resource = KecamatanResource::class;

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
                Section::make('Detail Kecamatan')
                    ->description('Informasi lengkap kecamatan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nama')
                                    ->label('Nama Kecamatan')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('primary'),
                                    
                                TextEntry::make('kode')
                                    ->label('Kode Kecamatan')
                                    ->badge()
                                    ->color('gray'),
                            ]),
                            
                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Tidak ada keterangan')
                            ->columnSpanFull(),
                            
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('jumlah_desa')
                                    ->label('Total Desa')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-building-office'),
                                    
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
                    
                Section::make('Daftar Desa')
                    ->description('Desa-desa yang berada di kecamatan ini')
                    ->schema([
                        TextEntry::make('desas')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                if ($record->desas->isEmpty()) {
                                    return 'Belum ada desa terdaftar';
                                }
                                
                                return $record->desas->map(function ($desa) {
                                    $status = $desa->is_active ? '✅' : '❌';
                                    return "{$status} {$desa->nama} ({$desa->jumlah_uttp} UTTP)";
                                })->join("\n");
                            })
                            ->listWithLineBreaks(),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }
}
