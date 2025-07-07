<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilTera extends Model
{
    use HasFactory;

    protected $fillable = [
        'permohonan_tera_id',
        'uttp_id',
        'hasil',
        'tanggal_tera',
        'nomor_sertifikat',
        'tanggal_expired',
        'petugas_tera',
        'catatan_hasil',
        'foto_hasil',
    ];

    protected $casts = [
        'tanggal_tera' => 'date',
        'tanggal_expired' => 'date',
        'foto_hasil' => 'array',
    ];

    // Relationships
    public function permohonanTera(): BelongsTo
    {
        return $this->belongsTo(PermohonanTera::class);
    }

    public function uttp(): BelongsTo
    {
        return $this->belongsTo(UTTP::class);
    }

    // Scopes
    public function scopeByHasil($query, $hasil)
    {
        return $query->where('hasil', $hasil);
    }

    public function scopeByPetugas($query, $petugas)
    {
        return $query->where('petugas_tera', $petugas);
    }

    public function scopeLulus($query)
    {
        return $query->where('hasil', 'Lulus');
    }

    public function scopeExpiredSoon($query, $days = 30)
    {
        return $query->where('hasil', 'Lulus')
                    ->whereNotNull('tanggal_expired')
                    ->whereDate('tanggal_expired', '<=', now()->addDays($days));
    }

    // Accessors
    public function getHasilBadgeAttribute()
    {
        return match($this->hasil) {
            'Lulus' => 'success',
            'Tidak Lulus' => 'danger',
            'Rusak' => 'warning',
            'Tidak Layak' => 'secondary',
            default => 'secondary'
        };
    }

    public function getIsExpiredSoonAttribute()
    {
        if (!$this->tanggal_expired || $this->hasil !== 'Lulus') {
            return false;
        }
        
        return $this->tanggal_expired->diffInDays(now()) <= 30;
    }

    public function getHasPhotosAttribute()
    {
        return !empty($this->foto_hasil) && is_array($this->foto_hasil) && count($this->foto_hasil) > 0;
    }

    // Boot method untuk auto-update UTTP dan PermohonanTera
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($model) {
            // Update status UTTP berdasarkan hasil tera
            $newStatus = match($model->hasil) {
                'Lulus' => 'Aktif',
                'Tidak Lulus' => 'Tidak Lulus',
                'Rusak' => 'Rusak',
                'Tidak Layak' => 'Tidak Layak',
                default => $model->uttp->status_tera
            };

            $updateData = [
                'status_tera' => $newStatus,
                'tanggal_tera_terakhir' => $model->tanggal_tera,
                'petugas_tera' => $model->petugas_tera,
            ];

            // Hanya update field sertifikat jika lulus
            if ($model->hasil === 'Lulus') {
                $updateData['tanggal_expired'] = $model->tanggal_expired;
                $updateData['nomor_sertifikat'] = $model->nomor_sertifikat;
            }

            $model->uttp->update($updateData);

            // Update status permohonan tera menjadi selesai
            $model->permohonanTera->update([
                'status' => 'Selesai'
            ]);

            // Generate sertifikat PDF jika lulus
            if ($model->hasil === 'Lulus' && $model->nomor_sertifikat) {
                static::generateCertificate($model);
            }

            // Kirim notifikasi
            static::sendNotification($model);
        });

        static::updated(function ($model) {
            // Update UTTP jika ada perubahan hasil
            if ($model->wasChanged('hasil')) {
                $newStatus = match($model->hasil) {
                    'Lulus' => 'Aktif',
                    'Tidak Lulus' => 'Tidak Lulus',
                    'Rusak' => 'Rusak',
                    'Tidak Layak' => 'Tidak Layak',
                    default => $model->uttp->status_tera
                };

                $model->uttp->update(['status_tera' => $newStatus]);
            }
        });
    }

    // Methods
    private static function generateCertificate($model)
    {
        // Logic untuk generate sertifikat PDF
        // Implementasi sesuai kebutuhan
    }

    private static function sendNotification($model)
    {
        // Logic untuk kirim notifikasi
        // Email ke pemilik UTTP, SMS, dll
    }

    public function canBeEdited()
    {
        return $this->created_at->diffInHours(now()) <= 24;
    }

    public function canBeDeleted()
    {
        return $this->created_at->diffInHours(now()) <= 2;
    }
}
