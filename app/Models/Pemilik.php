<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemilik extends Model
{
    use HasFactory;

    protected $table = 'pemiliks';

    protected $fillable = [
        'nama',
        'nik',
        'telepon',
        'alamat',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi: Satu pemilik bisa punya banyak UTTP
    public function uttps(): HasMany
    {
        return $this->hasMany(UTTP::class, 'pemilik_id');
    }

    // Relasi: UTTP aktif saja
    public function uttpsAktif(): HasMany
    {
        return $this->hasMany(UTTP::class, 'pemilik_id')
                    ->where('is_active', true);
    }

    // Scope
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    // Accessor
    public function getJumlahUttpAttribute()
    {
        return $this->uttps()->count();
    }

    public function getJumlahUttpAktifAttribute()
    {
        return $this->uttpsAktif()->count();
    }
}
