<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected static string $view = 'filament.widgets.quick-actions-widget';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';
    
    public function getViewData(): array
    {
        return [
            'actions' => [
                [
                    'label' => 'Tambah UTTP Baru',
                    'description' => 'Daftarkan UTTP baru ke sistem',
                    'icon' => '⚖️',
                    'url' => \App\Filament\Resources\UTTPResource::getUrl('create'),
                    'color' => 'blue',
                ],
                [
                    'label' => 'Buat Permohonan Tera',
                    'description' => 'Ajukan permohonan tera/tera ulang',
                    'icon' => '📝',
                    'url' => \App\Filament\Resources\PermohonanTeraResource::getUrl('create'),
                    'color' => 'green',
                ],
                [
                    'label' => 'Input Hasil Tera',
                    'description' => 'Catat hasil pemeriksaan tera',
                    'icon' => '✅',
                    'url' => \App\Filament\Resources\HasilTeraResource::getUrl('create'),
                    'color' => 'purple',
                ],
                [
                    'label' => 'Generate Laporan',
                    'description' => 'Buat laporan bulanan otomatis',
                    'icon' => '📊',
                    'url' => \App\Filament\Resources\LaporanBulananResource::getUrl('create'),
                    'color' => 'orange',
                ],
                [
                    'label' => 'Manajemen User',
                    'description' => 'Kelola pengguna sistem',
                    'icon' => '👥',
                    'url' => \App\Filament\Resources\UserResource::getUrl('index'),
                    'color' => 'red',
                ],
                [
                    'label' => 'Data Master',
                    'description' => 'Kelola data kecamatan, desa, pasar',
                    'icon' => '🗂️',
                    'url' => \App\Filament\Resources\KecamatanResource::getUrl('index'),
                    'color' => 'gray',
                ],
            ]
        ];
    }
}
