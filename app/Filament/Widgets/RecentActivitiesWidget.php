<?php

namespace App\Filament\Widgets;

use App\Models\PermohonanTera;
use App\Models\HasilTera;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentActivitiesWidget extends BaseWidget
{
    protected static ?string $heading = 'Aktivitas Terbaru';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                PermohonanTera::query()
                    ->with(['uttp.jenisUttp', 'uttp.desa.kecamatan'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M, H:i')
                    ->sortable()
                    ->size('sm'),
                    
                Tables\Columns\TextColumn::make('activity_type')
                    ->label('Aktivitas')
                    ->formatStateUsing(fn () => 'Permohonan Tera')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-document-plus'),
                    
                Tables\Columns\TextColumn::make('nomor_permohonan')
                    ->label('Nomor')
                    ->searchable()
                    ->copyable()
                    ->size('sm'),
                    
                Tables\Columns\TextColumn::make('uttp.nama_pemilik')
                    ->label('Pemilik')
                    ->searchable()
                    ->limit(20)
                    ->size('sm'),
                    
                Tables\Columns\TextColumn::make('uttp.jenisUttp.nama')
                    ->label('Jenis UTTP')
                    ->badge()
                    ->color('secondary')
                    ->size('sm'),
                    
                Tables\Columns\TextColumn::make('jenis_layanan')
                    ->label('Layanan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Di Kantor' => 'info',
                        'Luar Kantor' => 'warning',
                        'Sidang Tera' => 'success',
                        default => 'gray'
                    })
                    ->size('sm'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Disetujui' => 'info',
                        'Dijadwalkan' => 'primary',
                        'Selesai' => 'success',
                        'Ditolak' => 'danger',
                        default => 'gray'
                    })
                    ->size('sm'),
                    
                Tables\Columns\TextColumn::make('uttp.desa.kecamatan.nama')
                    ->label('Kecamatan')
                    ->size('sm')
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->size('sm')
                    ->url(fn (PermohonanTera $record): string => 
                        \App\Filament\Resources\PermohonanTeraResource::getUrl('view', ['record' => $record])
                    )
                    ->openUrlInNewTab(),
            ])
            ->paginated(false);
    }
}
