<?php

namespace App\Filament\Resources\UTTPResource\Pages;

use App\Filament\Resources\UTTPResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUTTP extends EditRecord
{
    protected static string $resource = UTTPResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
