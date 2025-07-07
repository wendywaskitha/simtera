<?php

namespace App\Filament\Resources\JenisUTTPResource\Pages;

use App\Filament\Resources\JenisUTTPResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJenisUTTPS extends ListRecords
{
    protected static string $resource = JenisUTTPResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
