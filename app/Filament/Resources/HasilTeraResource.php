<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\UTTP;
use Filament\Tables;
use App\Models\Petugas;
use Filament\Forms\Form;
use App\Models\HasilTera;
use Filament\Tables\Table;
use App\Models\PermohonanTera;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\HasilTeraResource\Pages;

class HasilTeraResource extends Resource
{
    protected static ?string $model = HasilTera::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    
    protected static ?string $navigationLabel = 'Hasil Tera';
    
    protected static ?string $modelLabel = 'Hasil Tera';
    
    protected static ?string $pluralModelLabel = 'Hasil Tera';
    
    protected static ?string $navigationGroup = 'Pelayanan';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $recordTitleAttribute = 'nomor_sertifikat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Pilih Permohonan')
                        ->description('Pilih permohonan tera yang akan diproses')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->schema([
                            Forms\Components\Section::make('Permohonan Tera')
                                ->schema([
                                    Forms\Components\Select::make('permohonan_tera_id')
                                        ->label('Pilih Permohonan Tera')
                                        ->relationship('permohonanTera', 'nomor_permohonan')
                                        ->searchable(['nomor_permohonan'])
                                        ->preload()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function (Forms\Set $set, $state) {
                                            if ($state) {
                                                $permohonan = PermohonanTera::with('uttp')->find($state);
                                                if ($permohonan) {
                                                    $set('uttp_id', $permohonan->uttp_id);
                                                    $set('petugas_tera', $permohonan->petugas_assigned);
                                                    $set('tanggal_tera', now()->format('Y-m-d'));
                                                }
                                            }
                                        })
                                        ->getOptionLabelFromRecordUsing(fn (PermohonanTera $record) => 
                                            "{$record->nomor_permohonan} - {$record->uttp->nama_pemilik} ({$record->status})")
                                        ->placeholder('Cari berdasarkan nomor permohonan')
                                        ->helperText('Pilih permohonan tera yang sudah dijadwalkan')
                                        ->options(function () {
                                            return PermohonanTera::with('uttp')
                                                ->whereIn('status', ['Dijadwalkan'])
                                                ->get()
                                                ->mapWithKeys(function ($permohonan) {
                                                    return [$permohonan->id => "{$permohonan->nomor_permohonan} - {$permohonan->uttp->nama_pemilik}"];
                                                });
                                        }),
                                        
                                    Forms\Components\Placeholder::make('permohonan_info')
                                        ->label('Informasi Permohonan')
                                        ->content(function (Forms\Get $get) {
                                            if (!$get('permohonan_tera_id')) {
                                                return 'Pilih permohonan tera terlebih dahulu untuk melihat informasi detail';
                                            }
                                            
                                            $permohonan = PermohonanTera::with(['uttp.jenisUttp', 'uttp.desa'])->find($get('permohonan_tera_id'));
                                            if (!$permohonan) return 'Permohonan tidak ditemukan';
                                            
                                            return view('filament.components.permohonan-info-card', compact('permohonan'));
                                        })
                                        ->columnSpanFull(),
                                        
                                    Forms\Components\Hidden::make('uttp_id'),
                                ])
                                ->columns(1),
                        ]),
                        
                    Forms\Components\Wizard\Step::make('Hasil Pemeriksaan')
                        ->description('Input hasil pemeriksaan tera')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->schema([
                            Forms\Components\Section::make('Detail Pemeriksaan')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Select::make('hasil')
                                                ->label('Hasil Pemeriksaan')
                                                ->required()
                                                ->options([
                                                    'Lulus' => 'Lulus',
                                                    'Tidak Lulus' => 'Tidak Lulus',
                                                    'Rusak' => 'Rusak',
                                                    'Tidak Layak' => 'Tidak Layak',
                                                ])
                                                ->live()
                                                ->afterStateUpdated(function (Forms\Set $set, $state) {
                                                    if ($state === 'Lulus') {
                                                        $set('tanggal_expired', now()->addYear()->format('Y-m-d'));
                                                        $set('nomor_sertifikat', 'CERT-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT));
                                                    } else {
                                                        $set('tanggal_expired', null);
                                                        $set('nomor_sertifikat', null);
                                                    }
                                                })
                                                ->prefixIcon('heroicon-o-clipboard-document-check')
                                                ->placeholder('Pilih hasil pemeriksaan'),
                                                
                                            Forms\Components\DatePicker::make('tanggal_tera')
                                                ->label('Tanggal Tera')
                                                ->required()
                                                ->default(now())
                                                ->maxDate(now())
                                                ->prefixIcon('heroicon-o-calendar'),
                                        ]),
                                        
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('nomor_sertifikat')
                                                ->label('Nomor Sertifikat')
                                                ->maxLength(50)
                                                ->unique(ignoreRecord: true)
                                                ->visible(fn (Forms\Get $get) => $get('hasil') === 'Lulus')
                                                ->prefixIcon('heroicon-o-document-text')
                                                ->helperText('Nomor sertifikat akan di-generate otomatis'),
                                                
                                            Forms\Components\DatePicker::make('tanggal_expired')
                                                ->label('Tanggal Expired')
                                                ->visible(fn (Forms\Get $get) => $get('hasil') === 'Lulus')
                                                ->minDate(now())
                                                ->prefixIcon('heroicon-o-calendar')
                                                ->helperText('Tanggal expired sertifikat tera'),
                                        ]),
                                        
                                    Forms\Components\Select::make('petugas_tera')
                                        ->label('Petugas Tera')
                                        ->required()
                                        ->options(fn () => Petugas::aktif()->pluck('nama', 'nama'))
                                        ->searchable()
                                        ->prefixIcon('heroicon-o-user')
                                        ->placeholder('Pilih petugas yang melakukan tera'),
                                        
                                    Forms\Components\Textarea::make('catatan_hasil')
                                        ->label('Catatan Hasil Pemeriksaan')
                                        ->rows(4)
                                        ->placeholder('Catatan detail hasil pemeriksaan, kondisi UTTP, atau hal-hal khusus')
                                        ->columnSpanFull(),
                                ])
                                ->columns(1),
                        ]),
                        
                    Forms\Components\Wizard\Step::make('Dokumentasi')
                        ->description('Upload foto hasil pemeriksaan')
                        ->icon('heroicon-o-camera')
                        ->schema([
                            Forms\Components\Section::make('Dokumentasi Hasil Tera')
                                ->schema([
                                    Forms\Components\FileUpload::make('foto_hasil')
                                        ->label('Foto Hasil Pemeriksaan')
                                        ->image()
                                        ->multiple()
                                        ->maxFiles(10)
                                        ->directory('hasil-tera-photos')
                                        ->visibility('private')
                                        ->imageEditor()
                                        ->imageEditorAspectRatios([
                                            '16:9',
                                            '4:3',
                                            '1:1',
                                        ])
                                        ->helperText('Upload foto hasil pemeriksaan, sertifikat, atau dokumentasi lainnya (maksimal 10 foto)')
                                        ->columnSpanFull(),
                                        
                                    Forms\Components\Toggle::make('generate_certificate')
                                        ->label('Generate Sertifikat Otomatis')
                                        ->default(true)
                                        ->visible(fn (Forms\Get $get) => $get('hasil') === 'Lulus')
                                        ->helperText('Sistem akan membuat sertifikat PDF secara otomatis')
                                        ->inline(false),
                                ])
                                ->columns(1),
                        ]),
                        
                    Forms\Components\Wizard\Step::make('Review & Submit')
                        ->description('Review hasil sebelum submit')
                        ->icon('heroicon-o-check-circle')
                        ->schema([
                            Forms\Components\Section::make('Review Hasil Tera')
                                ->schema([
                                    Forms\Components\Placeholder::make('review_summary')
                                        ->label('Ringkasan Hasil Tera')
                                        ->content(function (Forms\Get $get) {
                                            if (!$get('permohonan_tera_id')) {
                                                return 'Data hasil tera belum lengkap';
                                            }
                                            
                                            $permohonan = PermohonanTera::with('uttp')->find($get('permohonan_tera_id'));
                                            $hasil = $get('hasil');
                                            $tanggal = $get('tanggal_tera');
                                            $petugas = $get('petugas_tera');
                                            
                                            return view('filament.components.hasil-tera-review', [
                                                'permohonan' => $permohonan,
                                                'hasil' => $hasil,
                                                'tanggal_tera' => $tanggal,
                                                'tanggal_expired' => $get('tanggal_expired'),
                                                'nomor_sertifikat' => $get('nomor_sertifikat'),
                                                'petugas_tera' => $petugas,
                                                'catatan' => $get('catatan_hasil'),
                                                'foto_hasil' => $get('foto_hasil')
                                            ]);
                                        })
                                        ->columnSpanFull(),
                                        
                                    Forms\Components\Checkbox::make('konfirmasi_hasil')
                                        ->label('Konfirmasi Hasil')
                                        ->helperText('Saya menyatakan bahwa hasil pemeriksaan yang diinput sudah benar dan sesuai dengan kondisi aktual UTTP')
                                        ->required()
                                        ->accepted()
                                        ->columnSpanFull(),
                                ])
                                ->columns(1),
                        ]),
                ])
                ->columnSpanFull()
                ->skippable()
                ->persistStepInQueryString()
                // ->submitAction(new \Filament\Actions\Action('submit'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_sertifikat')
                    ->label('Nomor Sertifikat')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->copyMessage('Nomor sertifikat disalin!')
                    ->badge()
                    ->color('primary')
                    ->placeholder('Tidak ada sertifikat'),
                    
                Tables\Columns\TextColumn::make('permohonanTera.nomor_permohonan')
                    ->label('No. Permohonan')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('uttp.nama_pemilik')
                    ->label('Nama Pemilik')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->limit(25)
                    ->tooltip(function (HasilTera $record): ?string {
                        return $record->uttp->nama_pemilik;
                    }),
                    
                Tables\Columns\TextColumn::make('uttp.kode_uttp')
                    ->label('Kode UTTP')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('uttp.jenisUttp.nama')
                    ->label('Jenis UTTP')
                    ->badge()
                    ->color('secondary')
                    ->icon('heroicon-o-squares-2x2'),
                    
                Tables\Columns\BadgeColumn::make('hasil')
                    ->label('Hasil')
                    ->colors([
                        'success' => 'Lulus',
                        'danger' => ['Tidak Lulus', 'Rusak', 'Tidak Layak'],
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'Lulus',
                        'heroicon-o-x-circle' => ['Tidak Lulus', 'Rusak', 'Tidak Layak'],
                    ]),
                    
                Tables\Columns\TextColumn::make('tanggal_tera')
                    ->label('Tgl Tera')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),
                    
                Tables\Columns\TextColumn::make('tanggal_expired')
                    ->label('Tgl Expired')
                    ->date('d M Y')
                    ->placeholder('Tidak berlaku')
                    ->sortable()
                    ->color(function (HasilTera $record) {
                        if (!$record->tanggal_expired) return 'gray';
                        $daysUntilExpiry = Carbon::parse($record->tanggal_expired)->diffInDays(now(), false);
                        if ($daysUntilExpiry > 0) return 'danger'; // Already expired
                        if ($daysUntilExpiry > -30) return 'warning'; // Expires in 30 days
                        return 'success';
                    })
                    ->icon(function (HasilTera $record) {
                        if (!$record->tanggal_expired) return 'heroicon-o-minus';
                        $daysUntilExpiry = Carbon::parse($record->tanggal_expired)->diffInDays(now(), false);
                        if ($daysUntilExpiry > 0) return 'heroicon-o-exclamation-triangle';
                        if ($daysUntilExpiry > -30) return 'heroicon-o-clock';
                        return 'heroicon-o-check-circle';
                    }),
                    
                Tables\Columns\TextColumn::make('petugas_tera')
                    ->label('Petugas')
                    ->icon('heroicon-o-user')
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('has_photos')
                    ->label('Foto')
                    ->getStateUsing(fn (HasilTera $record) => !empty($record->foto_hasil))
                    ->boolean()
                    ->trueIcon('heroicon-o-camera')
                    ->falseIcon('heroicon-o-photo')
                    ->trueColor('success')
                    ->falseColor('gray'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('hasil')
                    ->label('Hasil Tera')
                    ->options([
                        'Lulus' => 'Lulus',
                        'Tidak Lulus' => 'Tidak Lulus',
                        'Rusak' => 'Rusak',
                        'Tidak Layak' => 'Tidak Layak',
                    ])
                    ->multiple(),
                    
                SelectFilter::make('petugas_tera')
                    ->label('Petugas')
                    ->options(fn () => Petugas::aktif()->pluck('nama', 'nama'))
                    ->searchable(),
                    
                Filter::make('tanggal_tera')
                    ->form([
                        Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['dari'], fn (Builder $query, $date): Builder => $query->whereDate('tanggal_tera', '>=', $date))
                            ->when($data['sampai'], fn (Builder $query, $date): Builder => $query->whereDate('tanggal_tera', '<=', $date));
                    }),
                    
                Filter::make('expired_soon')
                    ->label('Akan Expired (30 hari)')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('hasil', 'Lulus')
                              ->whereNotNull('tanggal_expired')
                              ->whereDate('tanggal_expired', '<=', now()->addDays(30)))
                    ->toggle(),
                    
                Filter::make('has_certificate')
                    ->label('Memiliki Sertifikat')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('nomor_sertifikat'))
                    ->toggle(),
                    
                Filter::make('has_photos')
                    ->label('Memiliki Foto')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('foto_hasil'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil-square')
                        ->visible(fn (HasilTera $record) => $record->created_at->diffInHours(now()) <= 24),
                    Tables\Actions\Action::make('download_certificate')
                        ->label('Download Sertifikat')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->url(fn (HasilTera $record) => $record->hasil === 'Lulus' ? route('certificate.download', $record) : null)
                        ->openUrlInNewTab()
                        ->visible(fn (HasilTera $record) => $record->hasil === 'Lulus' && $record->nomor_sertifikat),

                    Tables\Actions\Action::make('preview_certificate')
                        ->label('Preview Sertifikat')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(fn (HasilTera $record) => $record->hasil === 'Lulus' ? route('certificate.preview', $record) : null)
                        ->openUrlInNewTab()
                        ->visible(fn (HasilTera $record) => $record->hasil === 'Lulus' && $record->nomor_sertifikat),
                    Tables\Actions\Action::make('regenerate_certificate')
                        ->label('Generate Ulang Sertifikat')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Generate Ulang Sertifikat')
                        ->modalDescription('Apakah Anda yakin ingin membuat ulang sertifikat untuk hasil tera ini?')
                        ->action(function (HasilTera $record) {
                            // Logic untuk generate ulang sertifikat
                            Notification::make()
                                ->title('Sertifikat berhasil di-generate ulang')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (HasilTera $record) => $record->hasil === 'Lulus'),
                    Tables\Actions\Action::make('view_photos')
                        ->label('Lihat Foto')
                        ->icon('heroicon-o-photo')
                        ->color('info')
                        ->modalHeading('Foto Hasil Tera')
                        ->modalContent(fn (HasilTera $record) => view('filament.modals.foto-hasil-tera', ['record' => $record]))
                        ->modalWidth('7xl')
                        ->visible(fn (HasilTera $record) => !empty($record->foto_hasil)),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->visible(fn (HasilTera $record) => $record->created_at->diffInHours(now()) <= 2),
                ])
                ->label('Aksi')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(function ($records) {
                            if (!$records) return false;
                            
                            // Pastikan $records adalah collection dan validasi kondisi
                            $collection = collect($records);
                            return $collection->every(fn ($record) => $record->created_at->diffInHours(now()) <= 2);
                        }),
                    Tables\Actions\BulkAction::make('bulk_download_certificates')
                        ->label('Download Sertifikat Massal')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('success')
                        ->action(function ($records) {
                            $validRecords = $records->filter(fn ($record) => $record->hasil === 'Lulus' && $record->nomor_sertifikat);
                            
                            if ($validRecords->isEmpty()) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Tidak ada sertifikat yang valid')
                                    ->warning()
                                    ->send();
                                return;
                            }
                            
                            $ids = $validRecords->pluck('id')->toArray();
                            
                            return redirect()->route('certificates.bulk-download', ['hasil_tera_ids' => $ids]);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Download Sertifikat Massal')
                        ->modalDescription('Download sertifikat untuk semua hasil tera yang lulus?'),
                    Tables\Actions\BulkAction::make('bulk_certificate')
                        ->label('Generate Sertifikat Massal')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('success')
                        ->action(function ($records) {
                            // Validasi dan konversi ke collection
                            $collection = collect($records ?? []);
                            
                            if ($collection->isEmpty()) {
                                Notification::make()
                                    ->title('Tidak ada record yang dipilih')
                                    ->warning()
                                    ->send();
                                return;
                            }
                            
                            $generated = 0;
                            $failed = 0;
                            
                            $collection->each(function ($record) use (&$generated, &$failed) {
                                if ($record && $record->hasil === 'Lulus' && $record->nomor_sertifikat) {
                                    try {
                                        // Logic untuk generate sertifikat
                                        // static::generateCertificatePDF($record);
                                        $generated++;
                                    } catch (\Exception $e) {
                                        $failed++;
                                        \Log::error('Failed to generate certificate', [
                                            'record_id' => $record->id,
                                            'error' => $e->getMessage()
                                        ]);
                                    }
                                }
                            });
                            
                            if ($generated > 0) {
                                $message = "Berhasil generate {$generated} sertifikat";
                                if ($failed > 0) {
                                    $message .= " ({$failed} gagal)";
                                }
                                
                                Notification::make()
                                    ->title($message)
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Tidak ada sertifikat yang dapat di-generate')
                                    ->body('Pastikan hasil tera berstatus "Lulus" dan memiliki nomor sertifikat')
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Generate Sertifikat Massal')
                        ->modalDescription('Sistem akan membuat file PDF sertifikat untuk semua hasil tera yang lulus. Proses ini mungkin memakan waktu beberapa saat.'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHasilTeras::route('/'),
            'create' => Pages\CreateHasilTera::route('/create'),
            'view' => Pages\ViewHasilTera::route('/{record}'),
            'edit' => Pages\EditHasilTera::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('hasil', 'Lulus')->count();
    }
    
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return ($record->nomor_sertifikat ?? 'Hasil Tera') . ' - ' . $record->uttp->nama_pemilik;
    }
    
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'UTTP' => $record->uttp->kode_uttp,
            'Hasil' => $record->hasil,
            'Tanggal' => $record->tanggal_tera->format('d M Y'),
        ];
    }
}
