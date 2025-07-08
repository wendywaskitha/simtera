<?php

namespace App\Filament\Resources\PemilikResource\Pages;

use App\Filament\Resources\PemilikResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePemilik extends CreateRecord
{
    protected static string $resource = PemilikResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
