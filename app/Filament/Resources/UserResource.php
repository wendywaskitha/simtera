<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Hash;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Manajemen User';
    
    protected static ?string $modelLabel = 'User';
    
    protected static ?string $pluralModelLabel = 'Users';
    
    protected static ?string $navigationGroup = 'Sistem';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengguna')
                    ->description('Data identitas dan kontak pengguna')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama lengkap pengguna')
                                    ->prefixIcon('heroicon-o-user')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation !== 'create') {
                                            return;
                                        }
                                        $set('name', ucwords(strtolower($state)));
                                    }),
                                    
                                Forms\Components\TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('user@uptd-munbar.go.id')
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->helperText('Email akan digunakan untuk login dan notifikasi'),
                            ]),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->maxLength(15)
                                    ->placeholder('08123456789')
                                    ->prefixIcon('heroicon-o-phone')
                                    ->helperText('Nomor telepon untuk notifikasi SMS'),
                                    
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->helperText('Aktifkan untuk memberikan akses ke sistem')
                                    ->inline(false),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),
                    
                Forms\Components\Section::make('Keamanan & Akses')
                    ->description('Pengaturan password dan hak akses')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->placeholder('Minimal 8 karakter')
                                    ->prefixIcon('heroicon-o-lock-closed')
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->helperText('Kosongkan jika tidak ingin mengubah password'),
                                    
                                Forms\Components\TextInput::make('password_confirmation')
                                    ->label('Konfirmasi Password')
                                    ->password()
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->placeholder('Ulangi password')
                                    ->prefixIcon('heroicon-o-lock-closed')
                                    ->dehydrated(false)
                                    ->same('password')
                                    ->helperText('Masukkan ulang password untuk konfirmasi'),
                            ]),
                            
                        Forms\Components\Select::make('role')
                            ->label('Role Pengguna')
                            ->required()
                            ->options([
                                'admin' => 'Administrator',
                                'kepala' => 'Kepala UPTD',
                                'petugas' => 'Petugas Tera',
                                'staff' => 'Staff Administrasi',
                                'user' => 'User Biasa',
                            ])
                            ->searchable()
                            ->prefixIcon('heroicon-o-user-group')
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                // Auto-set permissions based on role
                                $permissions = match($state) {
                                    'admin' => ['view_any', 'create', 'update', 'delete', 'manage_users', 'view_reports'],
                                    'kepala' => ['view_any', 'view_reports', 'approve_requests'],
                                    'petugas' => ['view_any', 'create', 'update', 'input_results'],
                                    'staff' => ['view_any', 'create', 'update'],
                                    'user' => ['view_any'],
                                    default => []
                                };
                                $set('permissions', $permissions);
                            }),

                        // Tambahkan Placeholder untuk menampilkan deskripsi
                        Forms\Components\Placeholder::make('role_description')
                            ->label('Deskripsi Role')
                            ->content(function (Forms\Get $get) {
                                $role = $get('role');
                                
                                if (!$role) {
                                    return 'Pilih role untuk melihat deskripsi';
                                }
                                
                                $descriptions = [
                                    'admin' => '🛡️ **Administrator**: Akses penuh ke semua fitur sistem',
                                    'kepala' => '⭐ **Kepala UPTD**: Akses monitoring dan laporan',
                                    'petugas' => '🔧 **Petugas Tera**: Akses input hasil tera dan jadwal',
                                    'staff' => '📋 **Staff Administrasi**: Akses data entry dan administrasi',
                                    'user' => '👤 **User Biasa**: Akses terbatas untuk viewing',
                                ];
                                
                                return $descriptions[$role] ?? 'Deskripsi tidak tersedia';
                            })
                            ->visible(fn (Forms\Get $get) => $get('role'))
                            ->columnSpanFull(),

                            
                        Forms\Components\CheckboxList::make('permissions')
                            ->label('Permissions')
                            ->options([
                                'view_any' => 'View Data',
                                'create' => 'Create Data',
                                'update' => 'Update Data',
                                'delete' => 'Delete Data',
                                'manage_users' => 'Manage Users',
                                'view_reports' => 'View Reports',
                                'approve_requests' => 'Approve Requests',
                                'input_results' => 'Input Hasil Tera',
                            ])
                            ->descriptions([
                                'view_any' => 'Dapat melihat dan membaca data',
                                'create' => 'Dapat membuat data baru',
                                'update' => 'Dapat mengubah data existing',
                                'delete' => 'Dapat menghapus data',
                                'manage_users' => 'Dapat mengelola pengguna',
                                'view_reports' => 'Dapat melihat laporan',
                                'approve_requests' => 'Dapat menyetujui permohonan',
                                'input_results' => 'Dapat input hasil tera',
                            ])
                            ->columns(2)
                            ->gridDirection('row')
                            ->helperText('Pilih permission yang sesuai dengan role pengguna'),
                    ])
                    ->columns(1)
                    ->collapsible(),
                    
                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Data tambahan dan catatan')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\FileUpload::make('profile_photo_path')
                            ->label('Foto Profil')
                            ->image()
                            ->directory('profile-photos')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->helperText('Upload foto profil (maksimal 2MB)')
                            ->columnSpanFull(),
                            
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->placeholder('Catatan tambahan tentang pengguna')
                            ->columnSpanFull(),
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
                Tables\Columns\ImageColumn::make('profile_photo_path')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF')
                    ->size(40),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-o-user')
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email disalin!')
                    ->icon('heroicon-o-envelope'),
                    
                Tables\Columns\TextColumn::make('role_label')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state, $record): string => match ($record->role) {
                        'admin' => 'danger',
                        'kepala' => 'warning',
                        'petugas' => 'success',
                        'staff' => 'info',
                        'user' => 'gray',
                        default => 'secondary'
                    })
                    ->icon(fn (string $state, $record): string => match ($record->role) {
                        'admin' => 'heroicon-o-shield-check',
                        'kepala' => 'heroicon-o-star',
                        'petugas' => 'heroicon-o-wrench-screwdriver',
                        'staff' => 'heroicon-o-document-text',
                        'user' => 'heroicon-o-user',
                        default => 'heroicon-o-question-mark-circle'
                    }),
                    
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Nomor telepon disalin!')
                    ->icon('heroicon-o-phone')
                    ->placeholder('Tidak ada data'),
                    
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->email_verified_at))
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Login Terakhir')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Belum pernah login')
                    ->color(function ($record) {
                        if (!$record->last_login_at) return 'gray';
                        $daysSinceLogin = $record->last_login_at->diffInDays(now());
                        return $daysSinceLogin > 30 ? 'danger' : ($daysSinceLogin > 7 ? 'warning' : 'success');
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Administrator',
                        'kepala' => 'Kepala UPTD',
                        'petugas' => 'Petugas Tera',
                        'staff' => 'Staff Administrasi',
                        'user' => 'User Biasa',
                    ])
                    ->multiple(),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
                    
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email Verified')
                    ->placeholder('Semua Status')
                    ->trueLabel('Verified')
                    ->falseLabel('Belum Verified')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                    ),
                    
                Filter::make('inactive_users')
                    ->label('User Tidak Aktif (>30 hari)')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('last_login_at', '<', now()->subDays(30))
                              ->orWhereNull('last_login_at'))
                    ->toggle(),
                    
                Filter::make('created_this_month')
                    ->label('Dibuat Bulan Ini')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('created_at', now()->month))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil-square'),
                    Tables\Actions\Action::make('verify_email')
                        ->label('Verify Email')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Verify Email')
                        ->modalDescription('Apakah Anda yakin ingin memverifikasi email pengguna ini?')
                        ->action(function (User $record) {
                            $record->update(['email_verified_at' => now()]);
                            Notification::make()
                                ->title('Email berhasil diverifikasi')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (User $record) => is_null($record->email_verified_at)),
                    Tables\Actions\Action::make('reset_password')
                        ->label('Reset Password')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->form([
                            Forms\Components\TextInput::make('new_password')
                                ->label('Password Baru')
                                ->password()
                                ->required()
                                ->minLength(8)
                                ->placeholder('Minimal 8 karakter'),
                            Forms\Components\TextInput::make('confirm_password')
                                ->label('Konfirmasi Password')
                                ->password()
                                ->required()
                                ->same('new_password'),
                        ])
                        ->action(function (User $record, array $data) {
                            $record->update(['password' => Hash::make($data['new_password'])]);
                            Notification::make()
                                ->title('Password berhasil direset')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn (User $record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                        ->icon(fn (User $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn (User $record) => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(function (User $record) {
                            $record->update(['is_active' => !$record->is_active]);
                            $status = $record->is_active ? 'diaktifkan' : 'dinonaktifkan';
                            Notification::make()
                                ->title("User berhasil {$status}")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('send_email')
                        ->label('Kirim Email')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->url(fn (User $record) => "mailto:{$record->email}")
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
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            if (!$records) {
                                Notification::make()
                                    ->title('Tidak ada record yang dipilih')
                                    ->warning()
                                    ->send();
                                return;
                            }
                            
                            $updated = 0;
                            $records->each(function ($record) use (&$updated) {
                                if (!$record->is_active) {
                                    $record->update(['is_active' => true]);
                                    $updated++;
                                }
                            });
                            
                            Notification::make()
                                ->title("Berhasil mengaktifkan {$updated} pengguna")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            if (!$records) {
                                Notification::make()
                                    ->title('Tidak ada record yang dipilih')
                                    ->warning()
                                    ->send();
                                return;
                            }
                            
                            $updated = 0;
                            $records->each(function ($record) use (&$updated) {
                                if ($record->is_active) {
                                    $record->update(['is_active' => false]);
                                    $updated++;
                                }
                            });
                            
                            Notification::make()
                                ->title("Berhasil menonaktifkan {$updated} pengguna")
                                ->warning()
                                ->send();
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('verify_emails')
                        ->label('Verify Email Terpilih')
                        ->icon('heroicon-o-check-badge')
                        ->color('info')
                        ->action(function ($records) {
                            if (!$records) {
                                Notification::make()
                                    ->title('Tidak ada record yang dipilih')
                                    ->warning()
                                    ->send();
                                return;
                            }
                            
                            $updated = 0;
                            $records->each(function ($record) use (&$updated) {
                                if (is_null($record->email_verified_at)) {
                                    $record->update(['email_verified_at' => now()]);
                                    $updated++;
                                }
                            });
                            
                            Notification::make()
                                ->title("Berhasil memverifikasi {$updated} email")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export Data')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->action(function ($records) {
                            if (!$records || $records->isEmpty()) {
                                Notification::make()
                                    ->title('Tidak ada data untuk diexport')
                                    ->warning()
                                    ->send();
                                return;
                            }
                            
                            return response()->streamDownload(function () use ($records) {
                                echo "Nama,Email,Role,Status,Telepon,Dibuat\n";
                                foreach ($records as $record) {
                                    $status = $record->is_active ? 'Aktif' : 'Tidak Aktif';
                                    echo "{$record->name},{$record->email},{$record->role_label},{$status},{$record->phone},{$record->created_at->format('Y-m-d')}\n";
                                }
                            }, 'users-export-' . date('Y-m-d') . '.csv');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }
    
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name . ' (' . $record->role_label . ')';
    }
    
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Email' => $record->email,
            'Role' => $record->role_label,
            'Status' => $record->is_active ? 'Aktif' : 'Tidak Aktif',
        ];
    }
}
