<?php

namespace App\Filament\Resources\PermohonanTeraResource\Pages;

use App\Filament\Resources\PermohonanTeraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermohonanTera extends EditRecord
{
    protected static string $resource = PermohonanTeraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
