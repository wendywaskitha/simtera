<?php

namespace App\Filament\Resources\LaporanBulananResource\Pages;

use App\Filament\Resources\LaporanBulananResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanBulanans extends ListRecords
{
    protected static string $resource = LaporanBulananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
