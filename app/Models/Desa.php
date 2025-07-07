<?php

namespace App\Models;

use App\Models\UTTP;
use App\Models\Kecamatan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Desa extends Model
{
    use HasFactory;

    protected $fillable = [
        'kecamatan_id',
        'nama',
        'kode',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function uttps(): HasMany
    {
        return $this->hasMany(UTTP::class);
    }

    public function uttpsAktif(): HasMany
    {
        return $this->hasMany(UTTP::class)->where('is_active', true);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKecamatan($query, $kecamatanId)
    {
        return $query->where('kecamatan_id', $kecamatanId);
    }

    // Accessors
    public function getNamaLengkapAttribute()
    {
        return $this->nama . ', ' . $this->kecamatan->nama;
    }

    public function getJumlahUttpAttribute()
    {
        return $this->uttps()->count();
    }
}
