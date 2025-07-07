<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'phone',
        'profile_photo_path',
        'last_login_at',
        'permissions',
        'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'permissions' => 'array',
        ];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    // Accessors
    public function getRoleLabelAttribute()
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'kepala' => 'Kepala UPTD',
            'petugas' => 'Petugas Tera',
            'staff' => 'Staff Administrasi',
            'user' => 'User Biasa',
            default => 'User'
        };
    }

    public function getRoleBadgeAttribute()
    {
        return match($this->role) {
            'admin' => 'danger',
            'kepala' => 'warning',
            'petugas' => 'success',
            'staff' => 'info',
            'user' => 'gray',
            default => 'secondary'
        };
    }

    // Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isKepala()
    {
        return $this->role === 'kepala';
    }

    public function isPetugas()
    {
        return $this->role === 'petugas';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function hasPermission($permission)
    {
        if (!$this->permissions || !is_array($this->permissions)) {
            return false;
        }
        
        return in_array($permission, $this->permissions);
    }

    public function canAccessPanel(): bool
    {
        return $this->is_active && in_array($this->role, ['admin', 'kepala', 'petugas', 'staff']);
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            // Set default permissions based on role
            if (!$user->permissions) {
                $user->permissions = match($user->role) {
                    'admin' => ['view_any', 'create', 'update', 'delete', 'manage_users', 'view_reports'],
                    'kepala' => ['view_any', 'view_reports', 'approve_requests'],
                    'petugas' => ['view_any', 'create', 'update', 'input_results'],
                    'staff' => ['view_any', 'create', 'update'],
                    'user' => ['view_any'],
                    default => []
                };
            }
        });
    }
}
