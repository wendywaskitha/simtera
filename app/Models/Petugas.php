<?php

namespace App\Models;

use App\Models\HasilTera;
use App\Models\PermohonanTera;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Petugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'telepon',
        'email',
        'alamat',
        'kompetensi',
        'is_active',
    ];

    protected $casts = [
        'kompetensi' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function permohonanTeras(): HasMany
    {
        return $this->hasMany(PermohonanTera::class, 'petugas_assigned', 'nama');
    }

    public function hasilTeras(): HasMany
    {
        return $this->hasMany(HasilTera::class, 'petugas_tera', 'nama');
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKompetensi($query, $jenisUttp)
    {
        return $query->whereJsonContains('kompetensi', $jenisUttp);
    }

    // Accessors
    public function getJumlahTugasAktifAttribute()
    {
        return $this->permohonanTeras()
                    ->whereIn('status', ['Disetujui', 'Dijadwalkan'])
                    ->count();
    }

    public function getTotalTeraSelesaiAttribute()
    {
        return $this->hasilTeras()->count();
    }

    public function getKompetensiStringAttribute()
    {
        if (!$this->kompetensi || !is_array($this->kompetensi)) {
            return '-';
        }
        
        return implode(', ', $this->kompetensi);
    }

    // Methods
    public function canHandleJenisUttp($jenisUttp)
    {
        if (!$this->kompetensi || !is_array($this->kompetensi)) {
            return false;
        }
        
        return in_array($jenisUttp, $this->kompetensi);
    }

    public function assignPermohonan($permohonanId)
    {
        $permohonan = PermohonanTera::find($permohonanId);
        if ($permohonan) {
            $permohonan->update([
                'petugas_assigned' => $this->nama,
                'status' => 'Disetujui'
            ]);
        }
    }

    // Static methods
    public static function getAvailablePetugas($jenisUttp = null)
    {
        $query = static::aktif();
        
        if ($jenisUttp) {
            $query->whereJsonContains('kompetensi', $jenisUttp);
        }
        
        return $query->get();
    }

    public static function getPetugasWithLeastLoad()
    {
        return static::aktif()
                    ->withCount(['permohonanTeras' => function ($query) {
                        $query->whereIn('status', ['Disetujui', 'Dijadwalkan']);
                    }])
                    ->orderBy('permohonan_teras_count', 'asc')
                    ->first();
    }
}
