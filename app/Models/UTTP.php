<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Desa;
use App\Models\Pasar;
use App\Models\HasilTera;
use App\Models\JenisUTTP;
use App\Models\PermohonanTera;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UTTP extends Model
{
    use HasFactory;

    protected $table = 'uttps';

    protected $fillable = [
        'kode_uttp',
        'nama_pemilik',
        'nik_pemilik',
        'telepon_pemilik',
        'alamat_pemilik',
        'jenis_uttp_id',
        'merk',
        'tipe',
        'nomor_seri',
        'kapasitas_maksimum',
        'daya_baca',
        'tahun_pembuatan',
        'desa_id',
        'alamat_lengkap',
        'latitude',
        'longitude',
        'lokasi_type',
        'detail_lokasi',
        'status_tera',
        'tanggal_tera_terakhir',
        'tanggal_expired',
        'nomor_sertifikat',
        'petugas_tera',
        'foto_uttp',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'tanggal_tera_terakhir' => 'date',
        'tanggal_expired' => 'date',
        'foto_uttp' => 'array',
        'is_active' => 'boolean',
        'kapasitas_maksimum' => 'decimal:3',
        'daya_baca' => 'decimal:6',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function jenisUttp(): BelongsTo
    {
        return $this->belongsTo(JenisUTTP::class, 'jenis_uttp_id');
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    // Tambahkan di Model UTTP
    public function pasar(): BelongsTo
    {
        return $this->belongsTo(Pasar::class);
    }

    public function permohonanTeras(): HasMany
    {
        return $this->hasMany(PermohonanTera::class);
    }

    public function hasilTeras(): HasMany
    {
        return $this->hasMany(HasilTera::class);
    }

    public function hasilTeraAktif(): HasOne
    {
        return $this->hasOne(HasilTera::class)
                    ->where('hasil', 'Lulus')
                    ->latest('tanggal_tera');
    }


    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status_tera', $status);
    }

    public function scopeExpiredSoon($query, $days = 30)
    {
        return $query->where('tanggal_expired', '<=', Carbon::now()->addDays($days))
                    ->where('status_tera', 'Aktif');
    }

    public function scopeByLokasi($query, $type)
    {
        return $query->where('lokasi_type', $type);
    }

    public function scopeByDesa($query, $desaId)
    {
        return $query->where('desa_id', $desaId);
    }

    // Accessors
    public function getLokasiLengkapAttribute()
    {
        return $this->detail_lokasi . ', ' . $this->desa->nama_lengkap;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status_tera) {
            'Aktif' => 'success',
            'Expired' => 'danger',
            'Belum Tera' => 'warning',
            'Rusak' => 'danger',
            'Tidak Layak' => 'secondary',
            default => 'secondary'
        };
    }

    public function getIsExpiredSoonAttribute()
    {
        if (!$this->tanggal_expired || $this->status_tera !== 'Aktif') {
            return false;
        }

        return $this->tanggal_expired->diffInDays(Carbon::now()) <= 30;
    }

    // Boot method untuk auto-generate kode UTTP
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_uttp)) {
                $model->kode_uttp = static::generateKodeUttp();
            }
        });
    }

    private static function generateKodeUttp()
    {
        $year = date('Y');
        $lastUttp = static::whereYear('created_at', $year)
                         ->orderBy('id', 'desc')
                         ->first();

        $nextNumber = $lastUttp ? (int)substr($lastUttp->kode_uttp, -4) + 1 : 1;

        return 'UTP' . $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
