<?php

namespace App\Filament\Resources\HasilTeraResource\Pages;

use App\Filament\Resources\HasilTeraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHasilTera extends EditRecord
{
    protected static string $resource = HasilTeraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
