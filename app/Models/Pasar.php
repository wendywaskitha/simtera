<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasar extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'desa_id',
        'alamat',
        'latitude',
        'longitude',
        'kontak_person',
        'telepon',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
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
        return $query->whereHas('desa', fn ($q) => $q->where('kecamatan_id', $kecamatanId));
    }

    public function scopeHasCoordinates($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    // Accessors
    public function getAlamatLengkapAttribute()
    {
        return $this->alamat . ', ' . $this->desa->nama_lengkap;
    }

    public function getJumlahUttpAttribute()
    {
        return $this->uttps()->count();
    }

    public function getJumlahUttpAktifAttribute()
    {
        return $this->uttpsAktif()->count();
    }

    public function getHasCoordinatesAttribute()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function getGoogleMapsUrlAttribute()
    {
        if (!$this->has_coordinates) {
            return null;
        }
        
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    // Methods
    public function getDistanceFrom($latitude, $longitude)
    {
        if (!$this->has_coordinates) {
            return null;
        }
        
        $earthRadius = 6371; // km
        
        $dLat = deg2rad($latitude - $this->latitude);
        $dLon = deg2rad($longitude - $this->longitude);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
}
