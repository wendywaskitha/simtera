<?php

namespace App\Filament\Resources\HasilTeraResource\Pages;

use App\Filament\Resources\HasilTeraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHasilTeras extends ListRecords
{
    protected static string $resource = HasilTeraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
