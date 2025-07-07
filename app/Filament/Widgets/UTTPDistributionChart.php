<?php

namespace App\Filament\Widgets;

use App\Models\JenisUTTP;
use Filament\Widgets\ChartWidget;

class UTTPDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi UTTP per Jenis';
    protected static ?int $sort = 3;
    protected static string $color = 'warning';
    
    public function getDescription(): ?string
    {
        return 'Sebaran UTTP berdasarkan jenis dan status tera';
    }

    protected function getData(): array
    {
        $jenisUttp = JenisUTTP::aktif()
                             ->withCount(['uttps as total_uttp'])
                             ->withCount(['uttpsAktif as uttp_aktif'])
                             ->get();
        
        $labels = [];
        $totalData = [];
        $aktifData = [];
        $colors = [
            'rgba(59, 130, 246, 0.8)',   // Blue
            'rgba(16, 185, 129, 0.8)',   // Green  
            'rgba(245, 158, 11, 0.8)',   // Yellow
            'rgba(239, 68, 68, 0.8)',    // Red
            'rgba(139, 92, 246, 0.8)',   // Purple
            'rgba(236, 72, 153, 0.8)',   // Pink
            'rgba(14, 165, 233, 0.8)',   // Sky
            'rgba(34, 197, 94, 0.8)',    // Emerald
        ];

        foreach ($jenisUttp as $index => $jenis) {
            $labels[] = $jenis->nama;
            $totalData[] = $jenis->total_uttp;
            $aktifData[] = $jenis->uttp_aktif;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total UTTP',
                    'data' => $totalData,
                    'backgroundColor' => array_slice($colors, 0, count($labels)),
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'maintainAspectRatio' => false,
            'responsive' => true,
        ];
    }
}
