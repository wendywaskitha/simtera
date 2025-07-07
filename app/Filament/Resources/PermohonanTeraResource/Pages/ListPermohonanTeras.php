<?php

namespace App\Filament\Resources\PermohonanTeraResource\Pages;

use App\Filament\Resources\PermohonanTeraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermohonanTeras extends ListRecords
{
    protected static string $resource = PermohonanTeraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
