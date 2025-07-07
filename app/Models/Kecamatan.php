<?php

namespace App\Models;

use App\Models\Desa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kecamatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kode',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function desas(): HasMany
    {
        return $this->hasMany(Desa::class);
    }

    public function desasAktif(): HasMany
    {
        return $this->hasMany(Desa::class)->where('is_active', true);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getJumlahDesaAttribute()
    {
        return $this->desas()->count();
    }
}
