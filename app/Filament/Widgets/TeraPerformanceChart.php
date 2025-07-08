<?php

namespace App\Filament\Widgets;

use App\Models\HasilTera;
use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;

class TeraPerformanceChart extends ChartWidget
{
    protected static ?string $heading = 'Performa Tera 6 Bulan Terakhir';
    protected static ?int $sort = 2;
    protected static string $color = 'info';

    public function getDescription(): ?string
    {
        return 'Grafik perbandingan tera sah vs batal';
    }

    protected function getData(): array
    {
        $months = [];
        $teraSah = [];
        $teraBatal = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();

            $months[] = $date->format('M Y');

            $sah = HasilTera::whereBetween('tanggal_tera', [$startDate, $endDate])
                             ->where('hasil', 'Sah')
                             ->count();

            $batal = HasilTera::whereBetween('tanggal_tera', [$startDate, $endDate])
                                  ->where('hasil', '!=', 'Sah')
                                  ->count();

            $teraSah[] = $sah;
            $teraBatal[] = $batal;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tera Sah',
                    'data' => $teraSah,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
                [
                    'label' => 'Tera Batal',
                    'data' => $teraBatal,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'display' => true,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
        ];
    }
}
