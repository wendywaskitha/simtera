<?php

namespace App\Filament\Resources\PemilikResource\Pages;

use App\Filament\Resources\PemilikResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPemilik extends ViewRecord
{
    protected static string $resource = PemilikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pemilik')
                    ->schema([
                        Infolists\Components\TextEntry::make('nama')
                            ->label('Nama Pemilik'),
                        Infolists\Components\TextEntry::make('nik')
                            ->label('NIK')
                            ->placeholder('Tidak ada'),
                        Infolists\Components\TextEntry::make('telepon')
                            ->label('Telepon')
                            ->placeholder('Tidak ada'),
                        Infolists\Components\TextEntry::make('alamat')
                            ->label('Alamat'),
                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Status Aktif')
                            ->boolean(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Daftar UTTP')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('uttps')
                            ->label('UTTP yang Dimiliki')
                            ->schema([
                                Infolists\Components\TextEntry::make('kode_uttp')
                                    ->label('Kode UTTP')
                                    ->badge()
                                    ->color('primary'),
                                Infolists\Components\TextEntry::make('jenisUttp.nama')
                                    ->label('Jenis UTTP'),
                                Infolists\Components\TextEntry::make('status_tera')
                                    ->label('Status Tera')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Aktif' => 'success',
                                        'Expired' => 'danger',
                                        'Belum Tera' => 'warning',
                                        'Rusak' => 'danger',
                                        'Tidak Layak' => 'gray',
                                        default => 'gray'
                                    }),
                                Infolists\Components\TextEntry::make('lokasi_lengkap')
                                    ->label('Lokasi'),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->uttps()->count() > 0),
            ]);
    }
}
