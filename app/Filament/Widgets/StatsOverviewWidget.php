<?php

namespace App\Filament\Widgets;

use App\Models\UTTP;
use App\Models\PermohonanTera;
use App\Models\HasilTera;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        // Data statistik utama
        $totalUttp = UTTP::aktif()->count();
        $uttpAktif = UTTP::where('status_tera', 'Aktif')->count();
        $uttpExpiredSoon = UTTP::expiredSoon(30)->count();
        $permohonanPending = PermohonanTera::where('status', 'Pending')->count();
        
        // Data bulan ini
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();
        
        $teraBulanIni = HasilTera::whereBetween('tanggal_tera', [$startMonth, $endMonth])->count();
        $teraLulusBulanIni = HasilTera::whereBetween('tanggal_tera', [$startMonth, $endMonth])
                                    ->where('hasil', 'Lulus')->count();
        
        $persentaseLulus = $teraBulanIni > 0 ? round(($teraLulusBulanIni / $teraBulanIni) * 100, 1) : 0;
        
        // Trend calculations
        $lastMonth = Carbon::now()->subMonth();
        $teraLastMonth = HasilTera::whereBetween('tanggal_tera', [
            $lastMonth->startOfMonth(), 
            $lastMonth->endOfMonth()
        ])->count();
        
        $trendTera = $teraLastMonth > 0 ? 
            round((($teraBulanIni - $teraLastMonth) / $teraLastMonth) * 100, 1) : 0;

        return [
            Stat::make('Total UTTP Terdaftar', number_format($totalUttp))
                ->description('UTTP aktif dalam sistem')
                ->descriptionIcon('heroicon-m-scale')
                ->color('primary')
                ->chart([7, 12, 8, 15, 22, 18, $totalUttp % 30])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => '$dispatch("filterUttp", { filter: "all" })'
                ]),
                
            Stat::make('UTTP Tera Aktif', number_format($uttpAktif))
                ->description('Memiliki sertifikat tera valid')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([15, 18, 22, 25, 20, 28, $uttpAktif % 30])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => '$dispatch("filterUttp", { filter: "aktif" })'
                ]),
                
            Stat::make('Akan Expired', number_format($uttpExpiredSoon))
                ->description('Dalam 30 hari ke depan')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($uttpExpiredSoon > 50 ? 'danger' : ($uttpExpiredSoon > 20 ? 'warning' : 'success'))
                ->chart([5, 8, 12, 15, 18, 22, $uttpExpiredSoon])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => '$dispatch("filterUttp", { filter: "expired_soon" })'
                ]),
                
            Stat::make('Permohonan Pending', number_format($permohonanPending))
                ->description('Menunggu persetujuan')
                ->descriptionIcon('heroicon-m-clock')
                ->color($permohonanPending > 10 ? 'warning' : 'info')
                ->chart([3, 5, 8, 6, 10, 12, $permohonanPending])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => '$dispatch("filterPermohonan", { filter: "pending" })'
                ]),
                
            Stat::make('Tera Bulan Ini', number_format($teraBulanIni))
                ->description($trendTera >= 0 ? "+{$trendTera}% dari bulan lalu" : "{$trendTera}% dari bulan lalu")
                ->descriptionIcon($trendTera >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($trendTera >= 0 ? 'success' : 'danger')
                ->chart([10, 15, 20, 18, 25, 22, $teraBulanIni % 30]),
                
            Stat::make('Tingkat Keberhasilan', $persentaseLulus . '%')
                ->description('Persentase tera lulus bulan ini')
                ->descriptionIcon('heroicon-m-trophy')
                ->color($persentaseLulus >= 90 ? 'success' : ($persentaseLulus >= 75 ? 'warning' : 'danger'))
                ->chart([85, 88, 92, 89, 95, 91, $persentaseLulus]),
        ];
    }
}
