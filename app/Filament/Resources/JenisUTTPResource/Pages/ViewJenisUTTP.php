<?php

namespace App\Filament\Resources\JenisUTTPResource\Pages;

use App\Filament\Resources\JenisUTTPResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;

class ViewJenisUTTP extends ViewRecord
{
    protected static string $resource = JenisUTTPResource::class;

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
                Section::make('Detail Jenis UTTP')
                    ->description('Informasi lengkap jenis UTTP')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nama')
                                    ->label('Nama Jenis UTTP')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('primary'),
                                    
                                TextEntry::make('nama_lengkap')
                                    ->label('Nama Lengkap')
                                    ->weight('medium'),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('kode')
                                    ->label('Kode Jenis')
                                    ->badge()
                                    ->color('gray'),
                                    
                                TextEntry::make('satuan')
                                    ->label('Satuan')
                                    ->badge()
                                    ->color('info'),
                            ]),
                            
                        TextEntry::make('deskripsi')
                            ->label('Deskripsi')
                            ->placeholder('Tidak ada deskripsi')
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
                    
                Section::make('Statistik UTTP')
                    ->description('Statistik UTTP berdasarkan jenis ini')
                    ->schema([
                        TextEntry::make('uttps')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                if ($record->uttps->isEmpty()) {
                                    return 'Belum ada UTTP terdaftar dengan jenis ini';
                                }
                                
                                $statusCounts = $record->uttps->groupBy('status_tera')->map->count();
                                $result = [];
                                
                                foreach ($statusCounts as $status => $count) {
                                    $icon = match($status) {
                                        'Aktif' => '✅',
                                        'Expired' => '⚠️',
                                        'Belum Tera' => '🔄',
                                        default => '❌'
                                    };
                                    $result[] = "{$icon} {$status}: {$count} unit";
                                }
                                
                                return implode("\n", $result);
                            })
                            ->listWithLineBreaks(),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }
}
