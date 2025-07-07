<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PetugasResource\Pages;
use App\Models\Petugas;
use App\Models\JenisUTTP;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class PetugasResource extends Resource
{
    protected static ?string $model = Petugas::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Petugas';
    
    protected static ?string $modelLabel = 'Petugas';
    
    protected static ?string $pluralModelLabel = 'Petugas';
    
    protected static ?string $navigationGroup = 'Master Data';
    
    protected static ?int $navigationSort = 5;
    
    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Petugas')
                    ->description('Data identitas dan jabatan petugas tera')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nip')
                                    ->label('NIP')
                                    ->required()
                                    ->maxLength(20)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Contoh: 196801011990031001')
                                    ->prefixIcon('heroicon-o-identification')
                                    ->rules(['regex:/^\d{18}$/'])
                                    ->helperText('Format: 18 digit angka'),
                                    
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('Nama lengkap petugas')
                                    ->prefixIcon('heroicon-o-user')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation !== 'create') {
                                            return;
                                        }
                                        $set('nama', ucwords(strtolower($state)));
                                    }),
                            ]),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('jabatan')
                                    ->label('Jabatan')
                                    ->required()
                                    ->options([
                                        'Kepala UPTD Metrologi Legal' => 'Kepala UPTD Metrologi Legal',
                                        'Petugas Tera Senior' => 'Petugas Tera Senior',
                                        'Petugas Tera' => 'Petugas Tera',
                                        'Staff Administrasi' => 'Staff Administrasi',
                                        'Staff Teknis' => 'Staff Teknis',
                                    ])
                                    ->searchable()
                                    ->prefixIcon('heroicon-o-briefcase')
                                    ->placeholder('Pilih jabatan'),
                                    
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->helperText('Aktifkan untuk menampilkan petugas dalam sistem')
                                    ->inline(false),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),
                    
                Forms\Components\Section::make('Kontak & Alamat')
                    ->description('Informasi kontak dan alamat petugas')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('telepon')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->maxLength(15)
                                    ->placeholder('08123456789')
                                    ->prefixIcon('heroicon-o-phone')
                                    ->helperText('Nomor telepon aktif untuk komunikasi'),
                                    
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('petugas@uptd-munbar.go.id')
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->helperText('Email resmi untuk notifikasi sistem'),
                            ]),
                            
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->rows(3)
                            ->placeholder('Alamat tempat tinggal petugas')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible(),
                    
                Forms\Components\Section::make('Kompetensi & Keahlian')
                    ->description('Jenis UTTP yang dapat ditangani petugas')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Forms\Components\CheckboxList::make('kompetensi')
                            ->label('Kompetensi UTTP')
                            ->options(function () {
                                return JenisUTTP::aktif()->pluck('nama', 'nama')->toArray() + [
                                    'Administrasi' => 'Administrasi',
                                    'Koordinasi' => 'Koordinasi',
                                    'Supervisi' => 'Supervisi',
                                ];
                            })
                            ->descriptions([
                                'Timbangan Digital' => 'Timbangan elektronik untuk keperluan komersial',
                                'Timbangan Mekanik' => 'Timbangan analog/mekanik tradisional',
                                'Takaran BBM' => 'Alat takaran bahan bakar minyak di SPBU',
                                'Administrasi' => 'Pengelolaan dokumen dan administrasi',
                                'Koordinasi' => 'Koordinasi dan penjadwalan kegiatan',
                                'Supervisi' => 'Pengawasan dan evaluasi kinerja',
                            ])
                            ->columns(2)
                            ->gridDirection('row')
                            ->helperText('Pilih jenis UTTP atau tugas yang dapat ditangani petugas'),
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
                    ->label('Nama Petugas')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-o-user')
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('NIP disalin!'),
                    
                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-briefcase'),
                    
                Tables\Columns\TextColumn::make('telepon')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-o-phone')
                    ->placeholder('Tidak ada data')
                    ->copyable()
                    ->copyMessage('Nomor telepon disalin!'),
                    
                Tables\Columns\TextColumn::make('kompetensi_string')
                    ->label('Kompetensi')
                    ->toggleable()
                    ->wrap()
                    ->limit(50)
                    ->tooltip(function (Petugas $record): ?string {
                        return $record->kompetensi_string;
                    }),
                    
                Tables\Columns\TextColumn::make('jumlah_tugas_aktif')
                    ->label('Tugas Aktif')
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-o-clipboard-document-list'),
                    
                Tables\Columns\TextColumn::make('total_tera_selesai')
                    ->label('Total Tera')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),
                    
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
                Tables\Filters\SelectFilter::make('jabatan')
                    ->label('Jabatan')
                    ->options([
                        'Kepala UPTD Metrologi Legal' => 'Kepala UPTD',
                        'Petugas Tera Senior' => 'Petugas Senior',
                        'Petugas Tera' => 'Petugas Tera',
                        'Staff Administrasi' => 'Staff Admin',
                        'Staff Teknis' => 'Staff Teknis',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
                    
                Tables\Filters\Filter::make('has_email')
                    ->label('Memiliki Email')
                    ->query(fn ($query) => $query->whereNotNull('email'))
                    ->toggle(),
                    
                Tables\Filters\Filter::make('available_petugas')
                    ->label('Petugas Tersedia')
                    ->query(fn ($query) => $query->where('is_active', true)
                        ->whereHas('permohonanTeras', fn ($q) => $q->whereIn('status', ['Disetujui', 'Dijadwalkan']), '<', 3))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil-square'),
                    Tables\Actions\Action::make('send_email')
                        ->label('Kirim Email')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->url(fn (Petugas $record) => $record->email ? "mailto:{$record->email}" : null)
                        ->visible(fn (Petugas $record) => !is_null($record->email)),
                    Tables\Actions\Action::make('call_phone')
                        ->label('Telepon')
                        ->icon('heroicon-o-phone')
                        ->color('success')
                        ->url(fn (Petugas $record) => $record->telepon ? "tel:{$record->telepon}" : null)
                        ->visible(fn (Petugas $record) => !is_null($record->telepon)),
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
                        ->requiresConfirmation()
                        ->modalHeading('Aktifkan Petugas')
                        ->modalDescription('Apakah Anda yakin ingin mengaktifkan petugas yang dipilih?'),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation()
                        ->modalHeading('Nonaktifkan Petugas')
                        ->modalDescription('Petugas yang dinonaktifkan tidak akan muncul dalam assignment tugas.'),
                ]),
            ])
            ->defaultSort('nama', 'asc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPetugas::route('/'),
            'create' => Pages\CreatePetugas::route('/create'),
            'view' => Pages\ViewPetugas::route('/{record}'),
            'edit' => Pages\EditPetugas::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::aktif()->count();
    }
}
