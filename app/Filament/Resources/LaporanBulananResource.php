<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\UTTP;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\HasilTera;
use Filament\Tables\Table;
use App\Models\LaporanBulanan;
use App\Models\PermohonanTera;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LaporanBulananResource\Pages;

class LaporanBulananResource extends Resource
{
    protected static ?string $model = LaporanBulanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Laporan Bulanan';

    protected static ?string $modelLabel = 'Laporan Bulanan';

    protected static ?string $pluralModelLabel = 'Laporan Bulanan';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'periode_lengkap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Generate Laporan Bulanan')
                    ->description('Buat laporan bulanan otomatis berdasarkan data sistem')
                    ->icon('heroicon-o-document-chart-bar')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('tahun')
                                    ->label('Tahun')
                                    ->required()
                                    ->options(function () {
                                        $currentYear = date('Y');
                                        $years = [];
                                        for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++) {
                                            $years[$i] = $i;
                                        }
                                        return $years;
                                    })
                                    ->default(date('Y'))
                                    ->live()
                                    ->prefixIcon('heroicon-o-calendar'),

                                Forms\Components\Select::make('bulan')
                                    ->label('Bulan')
                                    ->required()
                                    ->options([
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                        4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ])
                                    ->default(date('n'))
                                    ->live()
                                    ->prefixIcon('heroicon-o-calendar-days'),
                            ]),

                        Forms\Components\Placeholder::make('preview_info')
                            ->label('Preview Laporan')
                            ->content(function (Forms\Get $get) {
                                if (!$get('tahun') || !$get('bulan')) {
                                    return 'Pilih tahun dan bulan untuk melihat preview laporan';
                                }

                                $tahun = $get('tahun');
                                $bulan = $get('bulan');
                                $namaBulan = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                    4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                    7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                    10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ][$bulan];

                                // Check if report already exists
                                $existingReport = LaporanBulanan::where('tahun', $tahun)
                                                               ->where('bulan', $bulan)
                                                               ->first();

                                if ($existingReport) {
                                    return view('filament.components.laporan-preview', [
                                        'laporan' => $existingReport,
                                        'isExisting' => true
                                    ]);
                                }

                                // Generate preview data
                                $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
                                $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

                                $totalUttp = UTTP::whereYear('created_at', '<=', $tahun)
                                                 ->whereMonth('created_at', '<=', $bulan)
                                                 ->count();

                                $totalTera = HasilTera::whereBetween('tanggal_tera', [$startDate, $endDate])
                                                     ->count();

                                $totalSah = HasilTera::whereBetween('tanggal_tera', [$startDate, $endDate])
                                                      ->where('hasil', 'Sah')
                                                      ->count();

                                return view('filament.components.laporan-preview', [
                                    'periode' => "{$namaBulan} {$tahun}",
                                    'total_uttp' => $totalUttp,
                                    'total_tera' => $totalTera,
                                    'total_sah' => $totalSah,
                                    'isExisting' => false
                                ]);
                            })
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('auto_generate')
                            ->label('Generate Otomatis')
                            ->default(true)
                            ->helperText('Sistem akan menghitung data secara otomatis berdasarkan periode yang dipilih')
                            ->inline(false),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Data Manual (Opsional)')
                    ->description('Override data otomatis jika diperlukan')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('total_uttp_terdaftar')
                                    ->label('Total UTTP Terdaftar')
                                    ->numeric()
                                    ->placeholder('Auto-calculated')
                                    ->helperText('Kosongkan untuk auto-calculate'),

                                Forms\Components\TextInput::make('total_tera_dilakukan')
                                    ->label('Total Tera Dilakukan')
                                    ->numeric()
                                    ->placeholder('Auto-calculated')
                                    ->helperText('Kosongkan untuk auto-calculate'),

                                Forms\Components\TextInput::make('total_permohonan')
                                    ->label('Total Permohonan')
                                    ->numeric()
                                    ->placeholder('Auto-calculated')
                                    ->helperText('Kosongkan untuk auto-calculate'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('total_tera_sah')
                                    ->label('Total Tera Sah')
                                    ->numeric()
                                    ->placeholder('Auto-calculated'),

                                Forms\Components\TextInput::make('total_tera_batal')
                                    ->label('Total Tera Batal')
                                    ->numeric()
                                    ->placeholder('Auto-calculated'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('periode_lengkap')
                    ->label('Periode')
                    ->searchable(['tahun', 'bulan'])
                    ->sortable(['tahun', 'bulan'])
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-o-calendar')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('total_uttp_terdaftar')
                    ->label('Total UTTP')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-scale'),

                Tables\Columns\TextColumn::make('total_tera_dilakukan')
                    ->label('Tera Dilakukan')
                    ->numeric()
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-o-clipboard-document-check'),

                Tables\Columns\TextColumn::make('total_tera_batal')
                    ->label('Batal')
                    ->numeric()
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),

                Tables\Columns\TextColumn::make('persentase_sah')
                    ->label('% Sah')
                    ->badge()
                    ->color(fn ($record) => $record->persentase_sah >= 90 ? 'success' :
                           ($record->persentase_sah >= 75 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn ($state) => $state . '%'),

                Tables\Columns\TextColumn::make('total_permohonan')
                    ->label('Permohonan')
                    ->numeric()
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(function () {
                        return LaporanBulanan::distinct()
                                           ->orderBy('tahun', 'desc')
                                           ->pluck('tahun', 'tahun')
                                           ->toArray();
                    }),

                SelectFilter::make('bulan')
                    ->label('Bulan')
                    ->options([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                        4 => 'April', 5 => 'Mei', 6 => 'Juni',
                        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ]),

                Filter::make('high_performance')
                    ->label('Performa Tinggi (≥90%)')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereRaw('(total_tera_sah / total_tera_dilakukan * 100) >= 90'))
                    ->toggle(),

                Filter::make('current_year')
                    ->label('Tahun Ini')
                    ->query(fn (Builder $query): Builder => $query->where('tahun', date('Y')))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil-square'),
                    Tables\Actions\Action::make('regenerate')
                        ->label('Generate Ulang')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Generate Ulang Laporan')
                        ->modalDescription('Data laporan akan di-generate ulang berdasarkan data terbaru sistem')
                        ->action(function (LaporanBulanan $record) {
                            $newReport = LaporanBulanan::generateLaporan($record->tahun, $record->bulan);
                            Notification::make()
                                ->title('Laporan berhasil di-generate ulang')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('export_pdf')
                        ->label('Export PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->url(fn (LaporanBulanan $record) => route('laporan.export.pdf', $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('export_excel')
                        ->label('Export Excel')
                        ->icon('heroicon-o-table-cells')
                        ->color('info')
                        ->url(fn (LaporanBulanan $record) => route('laporan.export.excel', $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash'),
                ])
                ->label('Aksi')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('bulk_regenerate')
                        ->label('Generate Ulang Terpilih')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->action(function ($records) {
                            if (!$records) {
                                Notification::make()
                                    ->title('Tidak ada record yang dipilih')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            $generated = 0;
                            $records->each(function ($record) use (&$generated) {
                                LaporanBulanan::generateLaporan($record->tahun, $record->bulan);
                                $generated++;
                            });

                            Notification::make()
                                ->title("Berhasil generate ulang {$generated} laporan")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('export_combined')
                        ->label('Export Gabungan')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('info')
                        ->action(function ($records) {
                            if (!$records || $records->isEmpty()) {
                                Notification::make()
                                    ->title('Tidak ada data untuk diexport')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            return response()->streamDownload(function () use ($records) {
                                echo "Periode,Total UTTP,Tera Dilakukan,Tera Sah,Persentase Sah,Total Permohonan\n";
                                foreach ($records as $record) {
                                    echo "{$record->periode_lengkap},{$record->total_uttp_terdaftar},{$record->total_tera_dilakukan},{$record->sah},{$record->persentase_sah}%,{$record->total_permohonan}\n";
                                }
                            }, 'laporan-bulanan-gabungan-' . date('Y-m-d') . '.csv');
                        }),
                ]),
            ])
            ->defaultSort('tahun', 'desc')
            ->defaultSort('bulan', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanBulanans::route('/'),
            'create' => Pages\CreateLaporanBulanan::route('/create'),
            'view' => Pages\ViewLaporanBulanan::route('/{record}'),
            'edit' => Pages\EditLaporanBulanan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('tahun', date('Y'))->count();
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return 'Laporan ' . $record->periode_lengkap;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Total UTTP' => $record->total_uttp_terdaftar,
            'Tera Sah' => $record->total_tera_sah,
            'Persentase' => $record->persentase_sah . '%',
        ];
    }
}
