<?php

namespace App\Models;

use App\Models\UTTP;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisUTTP extends Model
{
    use HasFactory;

    protected $table = 'jenis_uttps';

    protected $fillable = [
        'nama',
        'kode',
        'deskripsi',
        'satuan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function uttps(): HasMany
    {
        return $this->hasMany(UTTP::class, 'jenis_uttp_id');
    }

    public function uttpsAktif(): HasMany
    {
        return $this->hasMany(UTTP::class, 'jenis_uttp_id')->where('is_active', true);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getJumlahUttpAttribute()
    {
        return $this->uttps()->count();
    }

    public function getNamaLengkapAttribute()
    {
        return $this->nama . ($this->satuan ? ' (' . $this->satuan . ')' : '');
    }
}
