<?php

namespace App\Filament\Resources\JenisUTTPResource\Pages;

use App\Filament\Resources\JenisUTTPResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenisUTTP extends EditRecord
{
    protected static string $resource = JenisUTTPResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
