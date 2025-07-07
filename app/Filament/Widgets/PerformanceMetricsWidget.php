<?php

namespace App\Filament\Widgets;

use App\Models\HasilTera;
use App\Models\PermohonanTera;
use App\Models\Petugas;
use Filament\Widgets\Widget;
use Carbon\Carbon;

class PerformanceMetricsWidget extends Widget
{
    protected static string $view = 'filament.widgets.performance-metrics-widget';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    
    public function getViewData(): array
    {
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();
        
        // KPI Calculations
        $totalTera = HasilTera::whereBetween('tanggal_tera', [$startMonth, $endMonth])->count();
        $teraLulus = HasilTera::whereBetween('tanggal_tera', [$startMonth, $endMonth])
                             ->where('hasil', 'Lulus')->count();
        
        $tingkatKeberhasilan = $totalTera > 0 ? round(($teraLulus / $totalTera) * 100, 1) : 0;
        
        // Response Time (simulasi - sesuaikan dengan kebutuhan)
        $avgResponseTime = PermohonanTera::whereBetween('created_at', [$startMonth, $endMonth])
                                        ->whereNotNull('tanggal_jadwal')
                                        ->get()
                                        ->avg(function ($permohonan) {
                                            return $permohonan->tanggal_jadwal->diffInDays($permohonan->created_at);
                                        });
        
        // Petugas Performance
        $petugasPerformance = Petugas::aktif()
                                    ->withCount(['hasilTeras as total_tera' => function ($query) use ($startMonth, $endMonth) {
                                        $query->whereBetween('tanggal_tera', [$startMonth, $endMonth]);
                                    }])
                                    ->withCount(['hasilTeras as tera_lulus' => function ($query) use ($startMonth, $endMonth) {
                                        $query->whereBetween('tanggal_tera', [$startMonth, $endMonth])
                                              ->where('hasil', 'Lulus');
                                    }])
                                    ->having('total_tera', '>', 0)
                                    ->orderBy('total_tera', 'desc')
                                    ->take(5)
                                    ->get()
                                    ->map(function ($petugas) {
                                        $petugas->success_rate = $petugas->total_tera > 0 ? 
                                            round(($petugas->tera_lulus / $petugas->total_tera) * 100, 1) : 0;
                                        return $petugas;
                                    });
        
        // Target vs Achievement
        $monthlyTarget = 100; // Sesuaikan dengan target bulanan
        $achievement = round(($totalTera / $monthlyTarget) * 100, 1);
        
        return [
            'tingkat_keberhasilan' => $tingkatKeberhasilan,
            'avg_response_time' => round($avgResponseTime ?? 0, 1),
            'total_tera' => $totalTera,
            'tera_lulus' => $teraLulus,
            'monthly_target' => $monthlyTarget,
            'achievement' => $achievement,
            'petugas_performance' => $petugasPerformance,
        ];
    }
}
