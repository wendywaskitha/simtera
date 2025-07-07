<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Support\Enums\FontWeight;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('warning'),
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->color('danger'),
            Actions\Action::make('reset_password')
                ->label('Reset Password')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    // Generate random password
                    $newPassword = \Str::random(12);
                    $this->record->update(['password' => \Hash::make($newPassword)]);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Password berhasil direset')
                        ->body("Password baru: {$newPassword}")
                        ->success()
                        ->persistent()
                        ->send();
                }),
            Actions\Action::make('send_welcome_email')
                ->label('Kirim Email Welcome')
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->requiresConfirmation()
                ->action(function () {
                    // Logic untuk kirim welcome email
                    \Filament\Notifications\Notification::make()
                        ->title('Email welcome berhasil dikirim')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Profil Pengguna')
                    ->description('Informasi identitas dan kontak')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('profile_photo_path')
                                    ->label('Foto Profil')
                                    ->circular()
                                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF')
                                    ->size(120),
                                    
                                Grid::make(1)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Nama Lengkap')
                                            ->size('lg')
                                            ->weight(FontWeight::Bold)
                                            ->color('primary')
                                            ->icon('heroicon-o-user'),
                                            
                                        TextEntry::make('email')
                                            ->label('Email Address')
                                            ->copyable()
                                            ->copyMessage('Email disalin!')
                                            ->icon('heroicon-o-envelope'),
                                            
                                        TextEntry::make('phone')
                                            ->label('Nomor Telepon')
                                            ->placeholder('Tidak ada data')
                                            ->copyable()
                                            ->copyMessage('Nomor telepon disalin!')
                                            ->icon('heroicon-o-phone'),
                                    ])
                                    ->columnSpan(2),
                            ]),
                    ])
                    ->columns(1),
                    
                Section::make('Role & Permissions')
                    ->description('Hak akses dan permission pengguna')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('role_label')
                                    ->label('Role')
                                    ->badge()
                                    ->color(fn ($record): string => match ($record->role) {
                                        'admin' => 'danger',
                                        'kepala' => 'warning',
                                        'petugas' => 'success',
                                        'staff' => 'info',
                                        'user' => 'gray',
                                        default => 'secondary'
                                    })
                                    ->icon(fn ($record): string => match ($record->role) {
                                        'admin' => 'heroicon-o-shield-check',
                                        'kepala' => 'heroicon-o-star',
                                        'petugas' => 'heroicon-o-wrench-screwdriver',
                                        'staff' => 'heroicon-o-document-text',
                                        'user' => 'heroicon-o-user',
                                        default => 'heroicon-o-question-mark-circle'
                                    }),
                                    
                                TextEntry::make('permissions')
                                    ->label('Permissions')
                                    ->formatStateUsing(function ($record) {
                                        if (!$record->permissions || !is_array($record->permissions)) {
                                            return 'Tidak ada permission';
                                        }
                                        
                                        $permissionLabels = [
                                            'view_any' => 'View Data',
                                            'create' => 'Create Data',
                                            'update' => 'Update Data',
                                            'delete' => 'Delete Data',
                                            'manage_users' => 'Manage Users',
                                            'view_reports' => 'View Reports',
                                            'approve_requests' => 'Approve Requests',
                                            'input_results' => 'Input Hasil Tera',
                                        ];
                                        
                                        return collect($record->permissions)->map(function ($permission) use ($permissionLabels) {
                                            return $permissionLabels[$permission] ?? $permission;
                                        })->join(', ');
                                    })
                                    ->badge()
                                    ->color('info'),
                            ]),
                    ])
                    ->collapsible(),
                    
                Section::make('Status & Aktivitas')
                    ->description('Status akun dan aktivitas pengguna')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('is_active')
                                    ->label('Status Akun')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Tidak Aktif')
                                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                    
                                TextEntry::make('email_verified_at')
                                    ->label('Email Verified')
                                    ->badge()
                                    ->color(fn ($state): string => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn ($state): string => $state ? 'Verified' : 'Belum Verified')
                                    ->icon(fn ($state): string => $state ? 'heroicon-o-check-badge' : 'heroicon-o-x-circle'),
                                    
                                TextEntry::make('last_login_at')
                                    ->label('Login Terakhir')
                                    ->dateTime('d M Y, H:i')
                                    ->placeholder('Belum pernah login')
                                    ->icon('heroicon-o-clock'),
                                    
                                TextEntry::make('created_at')
                                    ->label('Bergabung Sejak')
                                    ->dateTime('d M Y')
                                    ->icon('heroicon-o-calendar'),
                            ]),
                    ])
                    ->collapsible(),
                    
                Section::make('Statistik Aktivitas')
                    ->description('Data aktivitas pengguna dalam sistem')
                    ->icon('heroicon-o-chart-pie')
                    ->schema([
                        TextEntry::make('activity_stats')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                // Simulasi data aktivitas - sesuaikan dengan kebutuhan
                                $stats = [
                                    'Total Login' => rand(10, 100),
                                    'Data Dibuat' => rand(5, 50),
                                    'Data Diupdate' => rand(10, 80),
                                    'Laporan Diakses' => rand(2, 20),
                                ];
                                
                                return view('filament.components.user-activity-stats', compact('stats'));
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),
                    
                Section::make('Catatan & Informasi Tambahan')
                    ->description('Catatan dan informasi tambahan')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Catatan')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpanFull(),
                            
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('updated_at')
                                    ->label('Terakhir Diperbarui')
                                    ->dateTime('d M Y, H:i')
                                    ->icon('heroicon-o-clock'),
                                    
                                TextEntry::make('days_since_created')
                                    ->label('Hari Sejak Bergabung')
                                    ->formatStateUsing(fn ($record) => $record->created_at->diffInDays(now()) . ' hari')
                                    ->icon('heroicon-o-calendar-days'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(true),
            ]);
    }
}
