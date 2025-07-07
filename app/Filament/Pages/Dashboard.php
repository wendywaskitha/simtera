<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    
    public function getTitle(): string
    {
        return 'Dashboard UPTD Metrologi Legal';
    }
    
    public function getSubheading(): ?string
    {
        return 'Kabupaten Muna Barat - ' . now()->format('d F Y');
    }
    
    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverviewWidget::class,
            \App\Filament\Widgets\TeraPerformanceChart::class,
            \App\Filament\Widgets\UTTPDistributionChart::class,
            \App\Filament\Widgets\PerformanceMetricsWidget::class,
            \App\Filament\Widgets\RecentActivitiesWidget::class,
            \App\Filament\Widgets\QuickActionsWidget::class,
        ];
    }
}
