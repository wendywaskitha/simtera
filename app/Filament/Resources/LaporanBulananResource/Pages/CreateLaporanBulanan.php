<?php

namespace App\Filament\Resources\LaporanBulananResource\Pages;

use App\Filament\Resources\LaporanBulananResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateLaporanBulanan extends CreateRecord
{
    protected static string $resource = LaporanBulananResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate laporan jika toggle aktif
        if ($data['auto_generate'] ?? true) {
            $generatedReport = \App\Models\LaporanBulanan::generateLaporan(
                $data['tahun'], 
                $data['bulan']
            );
            
            // Merge dengan data yang sudah ada
            $data = array_merge($generatedReport->toArray(), $data);
            
            Notification::make()
                ->title('Laporan berhasil di-generate otomatis')
                ->body('Data telah dihitung berdasarkan sistem')
                ->success()
                ->send();
        }
        
        // Remove auto_generate field
        unset($data['auto_generate']);
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
