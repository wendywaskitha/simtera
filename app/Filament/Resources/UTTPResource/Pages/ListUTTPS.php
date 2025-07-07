<?php

namespace App\Filament\Resources\UTTPResource\Pages;

use App\Filament\Resources\UTTPResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUTTPS extends ListRecords
{
    protected static string $resource = UTTPResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
