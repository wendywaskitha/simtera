<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PasarResource\Pages;
use App\Models\Pasar;
use App\Models\Kecamatan;
use App\Models\Desa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;

class PasarResource extends Resource
{
    protected static ?string $model = Pasar::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    
    protected static ?string $navigationLabel = 'Pasar';
    
    protected static ?string $modelLabel = 'Pasar';
    
    protected static ?string $pluralModelLabel = 'Pasar';
    
    protected static ?string $navigationGroup = 'Master Data';
    
    protected static ?int $navigationSort = 4;
    
    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pasar')
                    ->description('Data lengkap pasar untuk pelayanan sidang tera')
                    ->icon('heroicon-o-building-storefront')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Pasar')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('Contoh: Pasar Sentral Raha')
                                    ->prefixIcon('heroicon-o-building-storefront')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation !== 'create') {
                                            return;
                                        }
                                        $set('nama', ucwords(strtolower($state)));
                                    }),
                                    
                                Forms\Components\Select::make('desa_id')
                                    ->label('Desa/Kelurahan')
                                    ->relationship('desa', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->prefixIcon('heroicon-o-map-pin')
                                    ->createOptionForm([
                                        Forms\Components\Select::make('kecamatan_id')
                                            ->label('Kecamatan')
                                            ->relationship('kecamatan', 'nama')
                                            ->required(),
                                        Forms\Components\TextInput::make('nama')
                                            ->label('Nama Desa')
                                            ->required()
                                            ->maxLength(100),
                                    ])
                                    ->placeholder('Pilih desa lokasi pasar'),
                            ]),
                            
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->rows(3)
                            ->placeholder('Masukkan alamat lengkap pasar')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible(),
                    
                Forms\Components\Section::make('Koordinat & Lokasi')
                    ->description('Informasi geografis untuk navigasi petugas')
                    ->icon('heroicon-o-map')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->placeholder('-5.1234567')
                                    ->prefixIcon('heroicon-o-globe-alt')
                                    ->helperText('Koordinat lintang (contoh: -5.1234567)')
                                    ->rules(['regex:/^-?\d+\.\d+$/']),
                                    
                                Forms\Components\TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->placeholder('122.1234567')
                                    ->prefixIcon('heroicon-o-globe-alt')
                                    ->helperText('Koordinat bujur (contoh: 122.1234567)')
                                    ->rules(['regex:/^-?\d+\.\d+$/']),
                            ]),
                            
                        Forms\Components\Placeholder::make('map_info')
                            ->label('Informasi Peta')
                            ->content('Koordinat GPS akan membantu petugas menemukan lokasi pasar dengan mudah. Anda dapat menggunakan Google Maps untuk mendapatkan koordinat yang akurat.')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible(),
                    
                Forms\Components\Section::make('Kontak & Pengelola')
                    ->description('Informasi kontak untuk koordinasi sidang tera')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('kontak_person')
                                    ->label('Nama Pengelola/PIC')
                                    ->maxLength(100)
                                    ->placeholder('Nama pengelola pasar')
                                    ->prefixIcon('heroicon-o-user'),
                                    
                                Forms\Components\TextInput::make('telepon')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->maxLength(15)
                                    ->placeholder('08123456789')
                                    ->prefixIcon('heroicon-o-phone')
                                    ->helperText('Nomor telepon untuk koordinasi jadwal sidang tera'),
                            ]),
                            
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan Tambahan')
                            ->rows(3)
                            ->placeholder('Informasi tambahan tentang pasar (jam operasional, hari pasar, dll)')
                            ->columnSpanFull(),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Aktifkan untuk menampilkan pasar dalam sistem pelayanan')
                            ->inline(false),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Pasar')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-o-building-storefront')
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('desa.nama')
                    ->label('Desa')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('desa.kecamatan.nama')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                    
                Tables\Columns\TextColumn::make('kontak_person')
                    ->label('Pengelola')
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-o-user')
                    ->placeholder('Tidak ada data'),
                    
                Tables\Columns\TextColumn::make('telepon')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-o-phone')
                    ->placeholder('Tidak ada data'),
                    
                Tables\Columns\TextColumn::make('uttps_count')
                    ->label('Jumlah UTTP')
                    ->counts('uttps')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-scale'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('desa_id')
                    ->label('Desa')
                    ->relationship('desa', 'nama')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('kecamatan')
                    ->label('Kecamatan')
                    ->options(fn () => Kecamatan::aktif()->pluck('nama', 'id'))
                    ->query(function ($query, array $data) {
                        if ($data['value']) {
                            $query->whereHas('desa.kecamatan', fn ($q) => $q->where('id', $data['value']));
                        }
                    }),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
                    
                Tables\Filters\Filter::make('has_coordinates')
                    ->label('Memiliki Koordinat')
                    ->query(fn ($query) => $query->whereNotNull('latitude')->whereNotNull('longitude'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil-square'),
                    Tables\Actions\Action::make('view_map')
                        ->label('Lihat Peta')
                        ->icon('heroicon-o-map')
                        ->color('info')
                        ->url(fn (Pasar $record) => $record->latitude && $record->longitude 
                            ? "https://www.google.com/maps?q={$record->latitude},{$record->longitude}" 
                            : null)
                        ->openUrlInNewTab()
                        ->visible(fn (Pasar $record) => $record->latitude && $record->longitude),
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
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('nama', 'asc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPasars::route('/'),
            'create' => Pages\CreatePasar::route('/create'),
            'view' => Pages\ViewPasar::route('/{record}'),
            'edit' => Pages\EditPasar::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::aktif()->count();
    }
}
