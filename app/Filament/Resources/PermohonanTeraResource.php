<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\UTTP;
use Filament\Tables;
use App\Models\Petugas;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PermohonanTera;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PermohonanTeraResource\Pages;

class PermohonanTeraResource extends Resource
{
    protected static ?string $model = PermohonanTera::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Permohonan Tera';

    protected static ?string $modelLabel = 'Permohonan Tera';

    protected static ?string $pluralModelLabel = 'Permohonan Tera';

    protected static ?string $navigationGroup = 'Pelayanan';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nomor_permohonan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Pilih UTTP')
                        ->description('Pilih UTTP yang akan ditera')
                        ->icon('heroicon-o-scale')
                        ->schema([
                            Forms\Components\Section::make('Informasi UTTP')
                                ->schema([
                                    Forms\Components\Select::make('uttp_id')
                                        ->label('Pilih UTTP')
                                        ->relationship('uttp', 'kode_uttp')
                                        ->searchable(['kode_uttp', 'pemilik.nama', 'nomor_seri'])
                                        ->preload()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function (Forms\Set $set, $state) {
                                            if ($state) {
                                                $uttp = UTTP::find($state);
                                                if ($uttp) {
                                                    $set('jenis_layanan', $uttp->lokasi_type === 'Pasar' ? 'Sidang Tera' : 'Luar Kantor');
                                                }
                                            }
                                        })
                                        ->getOptionLabelFromRecordUsing(fn (UTTP $record) => "{$record->kode_uttp} - {$record->pemilik->nama} ({$record->jenisUttp->nama})")
                                        ->placeholder('Cari berdasarkan kode UTTP, nama pemilik, atau nomor seri')
                                        ->helperText('Pilih UTTP yang akan diajukan untuk tera/tera ulang'),

                                    Forms\Components\Placeholder::make('uttp_info')
                                        ->label('Informasi UTTP')
                                        ->content(function (Forms\Get $get) {
                                            if (!$get('uttp_id')) {
                                                return 'Pilih UTTP terlebih dahulu untuk melihat informasi detail';
                                            }

                                            $uttp = UTTP::find($get('uttp_id'));
                                            if (!$uttp) return 'UTTP tidak ditemukan';

                                            return view('filament.components.uttp-info-card', compact('uttp'));
                                        })
                                        ->columnSpanFull(),
                                ])
                                ->columns(1),
                        ]),

                    Forms\Components\Wizard\Step::make('Jenis Layanan')
                        ->description('Tentukan jenis layanan tera')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Forms\Components\Section::make('Pilihan Layanan')
                                ->schema([
                                    Forms\Components\Radio::make('jenis_layanan')
                                        ->label('Jenis Layanan')
                                        ->required()
                                        ->options([
                                            'Di Kantor' => 'Di Kantor',
                                            'Luar Kantor' => 'Luar Kantor (SPBU, Industri, dll)',
                                            'Sidang Tera' => 'Sidang Tera (Pasar)',
                                        ])
                                        ->descriptions([
                                            'Di Kantor' => 'UTTP dibawa ke kantor UPTD untuk ditera',
                                            'Luar Kantor' => 'Petugas datang ke lokasi UTTP (perlu surat permohonan)',
                                            'Sidang Tera' => 'Tera massal di pasar (tidak perlu surat permohonan)',
                                        ])
                                        ->live()
                                        ->afterStateUpdated(function (Forms\Set $set, $state) {
                                            if ($state === 'Sidang Tera') {
                                                $set('dokumen_pendukung', null);
                                            }
                                        })
                                        ->inline(false)
                                        ->columnSpanFull(),

                                    Forms\Components\Section::make('Informasi Layanan')
                                        ->schema([
                                            Forms\Components\Placeholder::make('layanan_detail')
                                                ->label('')
                                                ->content(function (Forms\Get $get) {
                                                    return match($get('jenis_layanan')) {
                                                        'Di Kantor' => '🏢 **Layanan di Kantor**: UTTP akan ditera di kantor UPTD. Pastikan UTTP dapat dibawa ke kantor.',
                                                        'Luar Kantor' => '🚛 **Layanan Luar Kantor**: Petugas akan datang ke lokasi UTTP. Wajib upload surat permohonan resmi.',
                                                        'Sidang Tera' => '🏪 **Sidang Tera**: Tera dilakukan di pasar secara massal. Tidak perlu surat permohonan.',
                                                        default => 'ℹ️ Pilih jenis layanan untuk melihat informasi detail'
                                                    };
                                                }),
                                        ])
                                        ->visible(fn (Forms\Get $get) => $get('jenis_layanan'))
                                        ->collapsible()
                                        ->collapsed(false)
                                        ->columnSpanFull(),
                                ])
                                ->columns(1),
                        ]),

                    Forms\Components\Wizard\Step::make('Dokumen & Jadwal')
                        ->description('Upload dokumen dan pilih jadwal')
                        ->icon('heroicon-o-document-arrow-up')
                        ->schema([
                            Forms\Components\Section::make('Upload Dokumen')
                                ->schema([
                                    Forms\Components\FileUpload::make('dokumen_pendukung')
                                        ->label('Surat Permohonan')
                                        ->disk('public')                // gunakan disk public
                                        ->directory('permohonan-tera')  // tersimpan di storage/app/public/permohonan-tera
                                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                        ->maxSize(5120)                 // 5 MB
                                        ->downloadable()                // link unduh otomatis
                                        ->previewable()
                                        ->helperText('PDF/JPG/PNG, maks 5 MB'),
                                    Forms\Components\Placeholder::make('dokumen_info')
                                        ->label('Informasi Dokumen')
                                        ->content('Untuk layanan sidang tera di pasar, tidak diperlukan surat permohonan karena dilakukan secara massal.')
                                        ->visible(fn (Forms\Get $get) => $get('jenis_layanan') === 'Sidang Tera')
                                        ->columnSpanFull(),
                                ])
                                ->columns(1),

                            Forms\Components\Section::make('Jadwal & Catatan')
                                ->schema([
                                    Forms\Components\DatePicker::make('tanggal_permohonan')
                                        ->label('Tanggal Permohonan')
                                        ->required()
                                        ->default(now())
                                        ->minDate(now())
                                        ->prefixIcon('heroicon-o-calendar'),

                                    Forms\Components\DatePicker::make('tanggal_jadwal')
                                        ->label('Tanggal Jadwal Diinginkan')
                                        ->minDate(now()->addDay())
                                        ->prefixIcon('heroicon-o-calendar')
                                        ->helperText('Kosongkan jika tidak ada preferensi jadwal khusus'),

                                    Forms\Components\Textarea::make('catatan_pemohon')
                                        ->label('Catatan Pemohon')
                                        ->rows(4)
                                        ->placeholder('Catatan khusus atau permintaan dari pemohon')
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),
                        ]),

                    Forms\Components\Wizard\Step::make('Review & Submit')
                        ->description('Review permohonan sebelum submit')
                        ->icon('heroicon-o-check-circle')
                        ->schema([
                            Forms\Components\Section::make('Review Permohonan')
                                ->schema([
                                    Forms\Components\Placeholder::make('review_summary')
                                        ->label('Ringkasan Permohonan')
                                        ->content(function (Forms\Get $get) {
                                            if (!$get('uttp_id')) {
                                                return 'Data permohonan belum lengkap';
                                            }

                                            $uttp = UTTP::find($get('uttp_id'));
                                            $jenis = $get('jenis_layanan');
                                            $tanggal = $get('tanggal_permohonan');

                                            return view('filament.components.permohonan-review', [
                                                'uttp' => $uttp,
                                                'jenis_layanan' => $jenis,
                                                'tanggal_permohonan' => $tanggal,
                                                'tanggal_jadwal' => $get('tanggal_jadwal'),
                                                'catatan' => $get('catatan_pemohon'),
                                                'dokumen' => $get('dokumen_pendukung')
                                            ]);
                                        })
                                        ->columnSpanFull(),

                                    Forms\Components\Checkbox::make('konfirmasi')
                                        ->label('Konfirmasi Data')
                                        ->helperText('Saya menyatakan bahwa data yang dimasukkan sudah benar dan sesuai')
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
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['uttp.pemilik']))
            ->columns([
                Tables\Columns\TextColumn::make('nomor_permohonan')
                    ->label('Nomor Permohonan')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->copyMessage('Nomor permohonan disalin!')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('uttp.pemilik.nama')
                    ->label('Nama Pemilik')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->limit(25)
                    ->tooltip(function (PermohonanTera $record): ?string {
                        return $record->uttp->pemilik->nama;
                    }),

                Tables\Columns\TextColumn::make('uttp.kode_uttp')
                    ->label('Kode UTTP')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->copyable(),

                Tables\Columns\TextColumn::make('uttp.jenisUttp.nama')
                    ->label('Jenis UTTP')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-squares-2x2'),

                Tables\Columns\BadgeColumn::make('jenis_layanan')
                    ->label('Jenis Layanan')
                    ->colors([
                        'info' => 'Di Kantor',
                        'warning' => 'Luar Kantor',
                        'success' => 'Sidang Tera',
                    ])
                    ->icons([
                        'heroicon-o-building-office' => 'Di Kantor',
                        'heroicon-o-truck' => 'Luar Kantor',
                        'heroicon-o-building-storefront' => 'Sidang Tera',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'Pending',
                        'info' => 'Disetujui',
                        'primary' => 'Dijadwalkan',
                        'success' => 'Selesai',
                        'danger' => 'Ditolak',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'Pending',
                        'heroicon-o-check-circle' => 'Disetujui',
                        'heroicon-o-calendar' => 'Dijadwalkan',
                        'heroicon-o-check-badge' => 'Selesai',
                        'heroicon-o-x-circle' => 'Ditolak',
                    ]),

                Tables\Columns\TextColumn::make('tanggal_permohonan')
                    ->label('Tgl Permohonan')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('tanggal_jadwal')
                    ->label('Tgl Jadwal')
                    ->date('d M Y')
                    ->placeholder('Belum dijadwalkan')
                    ->sortable()
                    ->color(function (PermohonanTera $record) {
                        if (!$record->tanggal_jadwal) return 'gray';
                        return $record->tanggal_jadwal->isPast() ? 'danger' : 'success';
                    })
                    ->icon('heroicon-o-calendar-days'),

                Tables\Columns\TextColumn::make('petugas_assigned')
                    ->label('Petugas')
                    ->placeholder('Belum ditugaskan')
                    ->icon('heroicon-o-user')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Disetujui' => 'Disetujui',
                        'Dijadwalkan' => 'Dijadwalkan',
                        'Selesai' => 'Selesai',
                        'Ditolak' => 'Ditolak',
                    ])
                    ->multiple(),

                SelectFilter::make('jenis_layanan')
                    ->label('Jenis Layanan')
                    ->options([
                        'Di Kantor' => 'Di Kantor',
                        'Luar Kantor' => 'Luar Kantor',
                        'Sidang Tera' => 'Sidang Tera',
                    ]),

                SelectFilter::make('petugas_assigned')
                    ->label('Petugas')
                    ->options(fn () => Petugas::aktif()->pluck('nama', 'nama'))
                    ->searchable(),

                Filter::make('tanggal_permohonan')
                    ->form([
                        Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['dari'], fn (Builder $query, $date): Builder => $query->whereDate('tanggal_permohonan', '>=', $date))
                            ->when($data['sampai'], fn (Builder $query, $date): Builder => $query->whereDate('tanggal_permohonan', '<=', $date));
                    }),

                Filter::make('pending_approval')
                    ->label('Perlu Persetujuan')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'Pending'))
                    ->toggle(),

                Filter::make('scheduled_today')
                    ->label('Jadwal Hari Ini')
                    ->query(fn (Builder $query): Builder => $query->whereDate('tanggal_jadwal', today()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil-square')
                        ->visible(fn (PermohonanTera $record) => in_array($record->status, ['Pending', 'Disetujui'])),
                    Tables\Actions\Action::make('approve')
                        ->label('Setujui')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Setujui Permohonan')
                        ->modalDescription('Apakah Anda yakin ingin menyetujui permohonan ini?')
                        ->action(function (PermohonanTera $record) {
                            $record->update(['status' => 'Disetujui']);
                            Notification::make()
                                ->title('Permohonan disetujui')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (PermohonanTera $record) => $record->status === 'Pending'),
                    Tables\Actions\Action::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('alasan_penolakan')
                                ->label('Alasan Penolakan')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (PermohonanTera $record, array $data) {
                            $record->update([
                                'status' => 'Ditolak',
                                'catatan_petugas' => $data['alasan_penolakan']
                            ]);
                            Notification::make()
                                ->title('Permohonan ditolak')
                                ->danger()
                                ->send();
                        })
                        ->visible(fn (PermohonanTera $record) => $record->status === 'Pending'),
                    Tables\Actions\Action::make('assign_petugas')
                        ->label('Tugaskan Petugas')
                        ->icon('heroicon-o-user-plus')
                        ->color('info')
                        ->form([
                            Forms\Components\Select::make('petugas')
                                ->label('Pilih Petugas')
                                ->options(fn () => Petugas::aktif()->pluck('nama', 'nama'))
                                ->required()
                                ->searchable(),
                            Forms\Components\DatePicker::make('tanggal_jadwal')
                                ->label('Tanggal Jadwal')
                                ->required()
                                ->minDate(now()),
                            Forms\Components\Textarea::make('catatan')
                                ->label('Catatan untuk Petugas')
                                ->rows(3),
                        ])
                        ->action(function (PermohonanTera $record, array $data) {
                            $record->update([
                                'petugas_assigned' => $data['petugas'],
                                'tanggal_jadwal' => $data['tanggal_jadwal'],
                                'status' => 'Dijadwalkan',
                                'catatan_petugas' => $data['catatan']
                            ]);
                            Notification::make()
                                ->title('Petugas berhasil ditugaskan')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (PermohonanTera $record) => $record->status === 'Disetujui'),
                    Tables\Actions\Action::make('download_dokumen')
                        ->label('Download Dokumen')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->url(fn (PermohonanTera $record) => $record->dokumen_pendukung ? route('download.dokumen', $record) : null)
                        ->openUrlInNewTab()
                        ->visible(fn (PermohonanTera $record) => !empty($record->dokumen_pendukung)),

                    // Tambahan action untuk multiple dokumen
                    Tables\Actions\Action::make('download_all_dokumen')
                        ->label('Download Semua Dokumen')
                        ->icon('heroicon-o-archive-box-arrow-down')
                        ->color('success')
                        ->url(fn (PermohonanTera $record) =>
                            is_array($record->dokumen_pendukung) && count($record->dokumen_pendukung) > 1
                                ? route('download.dokumen.multiple', $record)
                                : null
                        )
                        ->openUrlInNewTab()
                        ->visible(fn (PermohonanTera $record) =>
                            is_array($record->dokumen_pendukung) && count($record->dokumen_pendukung) > 1
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->visible(fn (PermohonanTera $record) => $record->status === 'Pending'),
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

                            // Pastikan $records adalah collection
                            $collection = collect($records);
                            return $collection->every(fn ($record) => $record->status === 'Pending');
                        }),
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Setujui Terpilih')
                        ->icon('heroicon-o-check-circle')
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

                            $updated = 0;
                            $collection->each(function ($record) use (&$updated) {
                                if ($record && $record->status === 'Pending') {
                                    $record->update(['status' => 'Disetujui']);
                                    $updated++;
                                }
                            });

                            if ($updated > 0) {
                                Notification::make()
                                    ->title("Berhasil menyetujui {$updated} permohonan")
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Tidak ada permohonan yang dapat disetujui')
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermohonanTeras::route('/'),
            'create' => Pages\CreatePermohonanTera::route('/create'),
            'view' => Pages\ViewPermohonanTera::route('/{record}'),
            'edit' => Pages\EditPermohonanTera::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'Pending')->count();
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->nomor_permohonan . ' - ' . $record->uttp->pemilik->nama;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'UTTP' => $record->uttp->kode_uttp,
            'Status' => $record->status,
            'Jenis Layanan' => $record->jenis_layanan,
        ];
    }
}
