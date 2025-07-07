<?php

namespace App\Filament\Resources\PermohonanTeraResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PermohonanTeraResource;

class CreatePermohonanTera extends CreateRecord
{
    protected static string $resource = PermohonanTeraResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return static::getModel()::create($data);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation
                // Hapus nomor_permohonan dan biarkan auto-generate
                unset($data['nomor_permohonan']);
                return static::getModel()::create($data);
            }
            throw $e;
        }
    }
}
