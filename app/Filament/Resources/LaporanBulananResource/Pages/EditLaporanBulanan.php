<?php

namespace App\Filament\Resources\LaporanBulananResource\Pages;

use App\Filament\Resources\LaporanBulananResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanBulanan extends EditRecord
{
    protected static string $resource = LaporanBulananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
