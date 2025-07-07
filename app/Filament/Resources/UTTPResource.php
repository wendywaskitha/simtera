<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Desa;
use App\Models\UTTP;
use Filament\Tables;
use App\Models\Pasar;
use Filament\Forms\Form;
use App\Models\JenisUTTP;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UTTPResource\Pages;

class UTTPResource extends Resource
{
    protected static ?string $model = UTTP::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    
    protected static ?string $navigationLabel = 'Data UTTP';
    
    protected static ?string $modelLabel = 'UTTP';
    
    protected static ?string $pluralModelLabel = 'UTTP';
    
    protected static ?string $navigationGroup = 'Data UTTP';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'kode_uttp';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Data Pemilik')
                        ->description('Informasi pemilik UTTP')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Forms\Components\Section::make('Identitas Pemilik')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('nama_pemilik')
                                                ->label('Nama Pemilik')
                                                ->required()
                                                ->maxLength(100)
                                                ->placeholder('Nama lengkap pemilik UTTP')
                                                ->prefixIcon('heroicon-o-user')
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                                    if ($operation !== 'create') {
                                                        return;
                                                    }
                                                    $set('nama_pemilik', ucwords(strtolower($state)));
                                                }),
                                                
                                            Forms\Components\TextInput::make('nik_pemilik')
                                                ->label('NIK Pemilik')
                                                ->maxLength(20)
                                                ->placeholder('Nomor Induk Kependudukan')
                                                ->prefixIcon('heroicon-o-identification')
                                                ->rules(['regex:/^\d{16}$/'])
                                                ->helperText('Format: 16 digit angka'),
                                        ]),
                                        
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('telepon_pemilik')
                                                ->label('Nomor Telepon')
                                                ->tel()
                                                ->maxLength(15)
                                                ->placeholder('08123456789')
                                                ->prefixIcon('heroicon-o-phone')
                                                ->helperText('Nomor telepon untuk notifikasi'),
                                                
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
                                                ->placeholder('Pilih desa lokasi UTTP'),
                                        ]),
                                        
                                    Forms\Components\Textarea::make('alamat_pemilik')
                                        ->label('Alamat Pemilik')
                                        ->required()
                                        ->rows(3)
                                        ->placeholder('Alamat lengkap pemilik UTTP')
                                        ->columnSpanFull(),
                                ])
                                ->columns(1),
                        ]),
                        
                    Forms\Components\Wizard\Step::make('Data UTTP')
                        ->description('Spesifikasi teknis UTTP')
                        ->icon('heroicon-o-scale')
                        ->schema([
                            Forms\Components\Section::make('Spesifikasi UTTP')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Select::make('jenis_uttp_id')
                                                ->label('Jenis UTTP')
                                                ->relationship('jenisUttp', 'nama')
                                                ->searchable()
                                                ->preload()
                                                ->required()
                                                ->prefixIcon('heroicon-o-squares-2x2')
                                                ->createOptionForm([
                                                    Forms\Components\TextInput::make('nama')
                                                        ->required()
                                                        ->maxLength(50),
                                                    Forms\Components\TextInput::make('kode')
                                                        ->maxLength(10),
                                                    Forms\Components\TextInput::make('satuan')
                                                        ->maxLength(20),
                                                ])
                                                ->placeholder('Pilih jenis UTTP'),
                                                
                                            Forms\Components\TextInput::make('nomor_seri')
                                                ->label('Nomor Seri')
                                                ->required()
                                                ->maxLength(50)
                                                ->unique(ignoreRecord: true)
                                                ->placeholder('Nomor seri unik UTTP')
                                                ->prefixIcon('heroicon-o-hashtag'),
                                        ]),
                                        
                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\TextInput::make('merk')
                                                ->label('Merk/Brand')
                                                ->maxLength(50)
                                                ->placeholder('Merk UTTP')
                                                ->prefixIcon('heroicon-o-tag'),
                                                
                                            Forms\Components\TextInput::make('tipe')
                                                ->label('Tipe/Model')
                                                ->maxLength(50)
                                                ->placeholder('Tipe atau model UTTP')
                                                ->prefixIcon('heroicon-o-cube'),
                                                
                                            Forms\Components\TextInput::make('tahun_pembuatan')
                                                ->label('Tahun Pembuatan')
                                                ->numeric()
                                                ->minValue(1900)
                                                ->maxValue(date('Y'))
                                                ->placeholder(date('Y'))
                                                ->prefixIcon('heroicon-o-calendar'),
                                        ]),
                                        
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('kapasitas_maksimum')
                                                ->label('Kapasitas Maksimum')
                                                ->numeric()
                                                ->step(0.001)
                                                ->placeholder('0.000')
                                                ->prefixIcon('heroicon-o-arrow-up-circle')
                                                ->helperText('Kapasitas maksimal dalam satuan yang sesuai'),
                                                
                                            Forms\Components\TextInput::make('daya_baca')
                                                ->label('Daya Baca/Ketelitian')
                                                ->numeric()
                                                ->step(0.000001)
                                                ->placeholder('0.000000')
                                                ->prefixIcon('heroicon-o-eye')
                                                ->helperText('Ketelitian pembacaan UTTP'),
                                        ]),
                                ])
                                ->columns(1),
                        ]),
                        
                    Forms\Components\Wizard\Step::make('Lokasi & Status')
                        ->description('Lokasi dan status tera UTTP')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Forms\Components\Section::make('Lokasi UTTP')
                                ->schema([
                                    Forms\Components\Radio::make('lokasi_type')
                                        ->label('Tipe Lokasi')
                                        ->required()
                                        ->options([
                                            'Pasar' => 'Pasar (Sidang Tera)',
                                            'Luar Pasar' => 'Luar Pasar (SPBU, Toko, dll)',
                                        ])
                                        ->descriptions([
                                            'Pasar' => 'UTTP di pasar tradisional - tidak perlu surat permohonan',
                                            'Luar Pasar' => 'UTTP di SPBU, toko, industri - perlu surat permohonan',
                                        ])
                                        ->live()
                                        ->afterStateUpdated(fn (Forms\Set $set) => $set('pasar_id', null))
                                        ->inline()
                                        ->columnSpanFull(),
                                        
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Select::make('pasar_id')
                                                ->label('Nama Pasar')
                                                ->relationship('pasar', 'nama')
                                                ->searchable()
                                                ->preload()
                                                ->prefixIcon('heroicon-o-building-storefront')
                                                ->createOptionForm([
                                                    Forms\Components\TextInput::make('nama')
                                                        ->required()
                                                        ->maxLength(100),
                                                    Forms\Components\Select::make('desa_id')
                                                        ->relationship('desa', 'nama')
                                                        ->required(),
                                                ])
                                                ->visible(fn (Forms\Get $get) => $get('lokasi_type') === 'Pasar')
                                                ->placeholder('Pilih pasar'),
                                                
                                            Forms\Components\TextInput::make('detail_lokasi')
                                                ->label(fn (Forms\Get $get) => $get('lokasi_type') === 'Pasar' ? 'Lokasi Kios/Lapak' : 'Detail Lokasi')
                                                ->maxLength(100)
                                                ->placeholder(fn (Forms\Get $get) => $get('lokasi_type') === 'Pasar' ? 'Contoh: Kios A-12' : 'Contoh: SPBU Shell Raha')
                                                ->prefixIcon('heroicon-o-map-pin'),
                                        ]),
                                        
                                    Forms\Components\Textarea::make('alamat_lengkap')
                                        ->label('Alamat Lengkap')
                                        ->required()
                                        ->rows(3)
                                        ->placeholder('Alamat lengkap lokasi UTTP')
                                        ->columnSpanFull(),
                                        
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('latitude')
                                                ->label('Latitude')
                                                ->numeric()
                                                ->placeholder('-5.1234567')
                                                ->prefixIcon('heroicon-o-globe-alt')
                                                ->helperText('Koordinat lintang (opsional)')
                                                ->rules(['regex:/^-?\d+\.\d+$/']),
                                                
                                            Forms\Components\TextInput::make('longitude')
                                                ->label('Longitude')
                                                ->numeric()
                                                ->placeholder('122.1234567')
                                                ->prefixIcon('heroicon-o-globe-alt')
                                                ->helperText('Koordinat bujur (opsional)')
                                                ->rules(['regex:/^-?\d+\.\d+$/']),
                                        ]),
                                ])
                                ->columns(1),
                                
                            Forms\Components\Section::make('Status Tera')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Select::make('status_tera')
                                                ->label('Status Tera')
                                                ->required()
                                                ->options([
                                                    'Belum Tera' => 'Belum Tera',
                                                    'Aktif' => 'Aktif',
                                                    'Expired' => 'Expired',
                                                    'Rusak' => 'Rusak',
                                                    'Tidak Layak' => 'Tidak Layak',
                                                ])
                                                ->default('Belum Tera')
                                                ->prefixIcon('heroicon-o-shield-check')
                                                ->live(),
                                                
                                            Forms\Components\Toggle::make('is_active')
                                                ->label('Status Aktif')
                                                ->default(true)
                                                ->helperText('Aktifkan untuk menampilkan UTTP dalam sistem')
                                                ->inline(false),
                                        ]),
                                        
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\DatePicker::make('tanggal_tera_terakhir')
                                                ->label('Tanggal Tera Terakhir')
                                                ->prefixIcon('heroicon-o-calendar')
                                                ->visible(fn (Forms\Get $get) => in_array($get('status_tera'), ['Aktif', 'Expired'])),
                                                
                                            Forms\Components\DatePicker::make('tanggal_expired')
                                                ->label('Tanggal Expired')
                                                ->prefixIcon('heroicon-o-calendar')
                                                ->visible(fn (Forms\Get $get) => in_array($get('status_tera'), ['Aktif', 'Expired'])),
                                        ]),
                                        
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('nomor_sertifikat')
                                                ->label('Nomor Sertifikat')
                                                ->maxLength(50)
                                                ->prefixIcon('heroicon-o-document-text')
                                                ->visible(fn (Forms\Get $get) => $get('status_tera') === 'Aktif'),
                                                
                                            Forms\Components\TextInput::make('petugas_tera')
                                                ->label('Petugas Tera')
                                                ->maxLength(100)
                                                ->prefixIcon('heroicon-o-user')
                                                ->visible(fn (Forms\Get $get) => in_array($get('status_tera'), ['Aktif', 'Expired'])),
                                        ]),
                                ])
                                ->columns(1),
                        ]),
                        
                    Forms\Components\Wizard\Step::make('Dokumentasi')
                        ->description('Upload foto dan keterangan')
                        ->icon('heroicon-o-camera')
                        ->schema([
                            Forms\Components\Section::make('Dokumentasi UTTP')
                                ->schema([
                                    Forms\Components\FileUpload::make('foto_uttp')
                                        ->label('Foto UTTP')
                                        ->image()
                                        ->multiple()
                                        ->maxFiles(5)
                                        ->directory('uttp-photos')
                                        ->visibility('public')
                                        ->imageEditor()
                                        ->imageEditorAspectRatios([
                                            '16:9',
                                            '4:3',
                                            '1:1',
                                        ])
                                        ->helperText('Upload maksimal 5 foto UTTP (format: JPG, PNG)')
                                        ->columnSpanFull(),
                                        
                                    Forms\Components\Textarea::make('keterangan')
                                        ->label('Keterangan Tambahan')
                                        ->rows(4)
                                        ->placeholder('Keterangan tambahan tentang UTTP, kondisi, atau catatan khusus')
                                        ->columnSpanFull(),
                                ])
                                ->columns(1),
                        ]),
                ])
                ->columnSpanFull()
                ->skippable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_uttp')
                    ->label('Kode UTTP')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->copyMessage('Kode UTTP disalin!')
                    ->badge()
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('nama_pemilik')
                    ->label('Nama Pemilik')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->icon('heroicon-o-user')
                    ->limit(25)
                    ->tooltip(function (UTTP $record): ?string {
                        return $record->nama_pemilik;
                    }),
                    
                Tables\Columns\TextColumn::make('jenisUttp.nama')
                    ->label('Jenis UTTP')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-squares-2x2'),
                    
                Tables\Columns\TextColumn::make('nomor_seri')
                    ->label('Nomor Seri')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Nomor seri disalin!')
                    ->badge()
                    ->color('gray'),
                    
                Tables\Columns\TextColumn::make('lokasi_lengkap')
                    ->label('Lokasi')
                    ->searchable(['detail_lokasi', 'alamat_lengkap'])
                    ->limit(30)
                    ->tooltip(function (UTTP $record): ?string {
                        return $record->lokasi_lengkap;
                    })
                    ->icon('heroicon-o-map-pin'),
                    
                Tables\Columns\BadgeColumn::make('status_tera')
                    ->label('Status Tera')
                    ->colors([
                        'success' => 'Aktif',
                        'warning' => 'Belum Tera',
                        'danger' => ['Expired', 'Rusak', 'Tidak Layak'],
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'Aktif',
                        'heroicon-o-clock' => 'Belum Tera',
                        'heroicon-o-exclamation-triangle' => 'Expired',
                        'heroicon-o-x-circle' => ['Rusak', 'Tidak Layak'],
                    ]),
                    
                Tables\Columns\TextColumn::make('tanggal_expired')
                    ->label('Expired')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable()
                    ->color(function (UTTP $record) {
                        if (!$record->tanggal_expired) return 'gray';
                        return $record->is_expired_soon ? 'warning' : 'success';
                    })
                    ->icon(function (UTTP $record) {
                        if (!$record->tanggal_expired) return 'heroicon-o-minus';
                        return $record->is_expired_soon ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-calendar';
                    }),
                    
                Tables\Columns\TextColumn::make('lokasi_type')
                    ->label('Tipe Lokasi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pasar' => 'success',
                        'Luar Pasar' => 'warning',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Pasar' => 'heroicon-o-building-storefront',
                        'Luar Pasar' => 'heroicon-o-building-office',
                    })
                    ->toggleable(),
                    
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
                SelectFilter::make('jenis_uttp_id')
                    ->label('Jenis UTTP')
                    ->relationship('jenisUttp', 'nama')
                    ->searchable()
                    ->preload(),
                    
                SelectFilter::make('status_tera')
                    ->label('Status Tera')
                    ->options([
                        'Belum Tera' => 'Belum Tera',
                        'Aktif' => 'Aktif',
                        'Expired' => 'Expired',
                        'Rusak' => 'Rusak',
                        'Tidak Layak' => 'Tidak Layak',
                    ])
                    ->multiple(),
                    
                SelectFilter::make('lokasi_type')
                    ->label('Tipe Lokasi')
                    ->options([
                        'Pasar' => 'Pasar',
                        'Luar Pasar' => 'Luar Pasar',
                    ]),
                    
                SelectFilter::make('desa_id')
                    ->label('Desa')
                    ->relationship('desa', 'nama')
                    ->searchable()
                    ->preload(),
                    
                Filter::make('expired_soon')
                    ->label('Akan Expired (30 hari)')
                    ->query(fn (Builder $query): Builder => $query->expiredSoon())
                    ->toggle(),
                    
                Filter::make('has_coordinates')
                    ->label('Memiliki Koordinat GPS')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('latitude')->whereNotNull('longitude'))
                    ->toggle(),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil-square'),
                    Tables\Actions\Action::make('view_location')
                        ->label('Lihat Lokasi')
                        ->icon('heroicon-o-map')
                        ->color('info')
                        ->url(fn (UTTP $record) => $record->latitude && $record->longitude 
                            ? "https://www.google.com/maps?q={$record->latitude},{$record->longitude}" 
                            : null)
                        ->openUrlInNewTab()
                        ->visible(fn (UTTP $record) => $record->latitude && $record->longitude),
                    Tables\Actions\Action::make('create_permohonan')
                        ->label('Buat Permohonan Tera')
                        ->icon('heroicon-o-document-plus')
                        ->color('success')
                        ->url(fn (UTTP $record) => \App\Filament\Resources\PermohonanTeraResource::getUrl('create', ['uttp_id' => $record->id]))
                        ->visible(fn (UTTP $record) => in_array($record->status_tera, ['Belum Tera', 'Expired'])),
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
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export Data')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function ($records) {
                            // Implementasi export Excel/CSV
                            return response()->streamDownload(function () use ($records) {
                                echo "Kode UTTP,Nama Pemilik,Jenis UTTP,Status Tera,Lokasi\n";
                                foreach ($records as $record) {
                                    echo "{$record->kode_uttp},{$record->nama_pemilik},{$record->jenisUttp->nama},{$record->status_tera},{$record->lokasi_lengkap}\n";
                                }
                            }, 'uttp-export-' . date('Y-m-d') . '.csv');
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUTTPs::route('/'),
            'create' => Pages\CreateUTTP::route('/create'),
            'view' => Pages\ViewUTTP::route('/{record}'),
            'edit' => Pages\EditUTTP::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::aktif()->count();
    }
    
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->kode_uttp . ' - ' . $record->nama_pemilik;
    }
    
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Jenis' => $record->jenisUttp->nama,
            'Status' => $record->status_tera,
            'Lokasi' => $record->lokasi_lengkap,
        ];
    }
}
