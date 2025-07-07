<?php

namespace App\Filament\Resources\LaporanBulananResource\Pages;

use App\Filament\Resources\LaporanBulananResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Support\Enums\FontWeight;

class ViewLaporanBulanan extends ViewRecord
{
    protected static string $resource = LaporanBulananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('warning'),
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->color('danger'),
            Actions\Action::make('regenerate')
                ->label('Generate Ulang')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    \App\Models\LaporanBulanan::generateLaporan($this->record->tahun, $this->record->bulan);
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                }),
            Actions\Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn () => route('laporan.export.pdf', $this->record))
                ->openUrlInNewTab(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Ringkasan Laporan')
                    ->description('Overview statistik bulanan')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('periode_lengkap')
                                    ->label('Periode Laporan')
                                    ->size('lg')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->icon('heroicon-o-calendar'),
                                    
                                TextEntry::make('persentase_lulus')
                                    ->label('Tingkat Keberhasilan')
                                    ->formatStateUsing(fn ($state) => $state . '%')
                                    ->badge()
                                    ->color(fn ($state) => $state >= 90 ? 'success' : 
                                           ($state >= 75 ? 'warning' : 'danger'))
                                    ->size('lg')
                                    ->icon('heroicon-o-trophy'),
                            ]),
                    ])
                    ->columns(1),
                    
                Section::make('Statistik Utama')
                    ->description('Data statistik kinerja bulanan')
                    ->icon('heroicon-o-chart-pie')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('total_uttp_terdaftar')
                                    ->label('Total UTTP Terdaftar')
                                    ->numeric()
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-scale'),
                                    
                                TextEntry::make('total_tera_dilakukan')
                                    ->label('Tera Dilakukan')
                                    ->numeric()
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-clipboard-document-check'),
                                    
                                TextEntry::make('total_tera_lulus')
                                    ->label('Tera Lulus')
                                    ->numeric()
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-check-circle'),
                                    
                                TextEntry::make('total_permohonan')
                                    ->label('Total Permohonan')
                                    ->numeric()
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-document-text'),
                            ]),
                    ])
                    ->columns(1),
                    
                Section::make('Analisis Per Jenis UTTP')
                    ->description('Breakdown berdasarkan jenis UTTP')
                    ->icon('heroicon-o-squares-2x2')
                    ->schema([
                        TextEntry::make('detail_per_jenis')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                if (!$record->detail_per_jenis || !is_array($record->detail_per_jenis)) {
                                    return 'Data detail per jenis tidak tersedia';
                                }
                                
                                return view('filament.components.detail-per-jenis-chart', [
                                    'data' => $record->detail_per_jenis
                                ]);
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                    
                Section::make('Analisis Per Lokasi')
                    ->description('Breakdown berdasarkan kecamatan')
                    ->icon('heroicon-o-map')
                    ->schema([
                        TextEntry::make('detail_per_lokasi')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                if (!$record->detail_per_lokasi || !is_array($record->detail_per_lokasi)) {
                                    return 'Data detail per lokasi tidak tersedia';
                                }
                                
                                return view('filament.components.detail-per-lokasi-chart', [
                                    'data' => $record->detail_per_lokasi
                                ]);
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                    
                Section::make('Trend Analysis')
                    ->description('Analisis trend dan perbandingan')
                    ->icon('heroicon-o-chart-bar-square')
                    ->schema([
                        TextEntry::make('trend_analysis')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                // Get previous month data for comparison
                                $prevMonth = $record->bulan == 1 ? 12 : $record->bulan - 1;
                                $prevYear = $record->bulan == 1 ? $record->tahun - 1 : $record->tahun;
                                
                                $prevReport = \App\Models\LaporanBulanan::where('tahun', $prevYear)
                                                                       ->where('bulan', $prevMonth)
                                                                       ->first();
                                
                                return view('filament.components.trend-analysis', [
                                    'current' => $record,
                                    'previous' => $prevReport
                                ]);
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),
                    
                Section::make('Informasi Sistem')
                    ->description('Data audit dan tracking')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Laporan Dibuat')
                                    ->dateTime('d M Y, H:i')
                                    ->icon('heroicon-o-calendar'),
                                    
                                TextEntry::make('updated_at')
                                    ->label('Terakhir Diperbarui')
                                    ->dateTime('d M Y, H:i')
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(true),
            ]);
    }
}
