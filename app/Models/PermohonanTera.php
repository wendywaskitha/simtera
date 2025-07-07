<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class PermohonanTera extends Model
{
    protected $fillable = [
        'nomor_permohonan', 'uttp_id', 'jenis_layanan', 'status',
        'tanggal_permohonan', 'tanggal_jadwal', 'catatan_pemohon',
        'catatan_petugas', 'dokumen_pendukung', 'petugas_assigned'
    ];

    protected $casts = [
        'tanggal_permohonan' => 'date',
        'tanggal_jadwal' => 'date',
        'dokumen_pendukung' => 'array',
    ];

    // Relationships
    public function uttp(): BelongsTo
    {
        return $this->belongsTo(UTTP::class);
    }

    public function hasilTera(): HasOne
    {
        return $this->hasOne(HasilTera::class);
    }

    // Boot method dengan logic yang diperbaiki
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->nomor_permohonan)) {
                $model->nomor_permohonan = static::generateNomorPermohonan();
            }
        });
    }

    // Method generate nomor yang thread-safe
    private static function generateNomorPermohonan()
    {
        $maxAttempts = 10;
        $attempt = 0;
        
        do {
            $attempt++;
            $nomor = static::createNomorPermohonan();
            
            // Cek apakah nomor sudah ada
            $exists = static::where('nomor_permohonan', $nomor)->exists();
            
            if (!$exists) {
                return $nomor;
            }
            
            // Jika sudah ada, tunggu sebentar dan coba lagi
            usleep(100000); // 100ms
            
        } while ($attempt < $maxAttempts);
        
        // Jika masih gagal, gunakan timestamp untuk uniqueness
        return static::createNomorPermohonanWithTimestamp();
    }

    private static function createNomorPermohonan()
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        
        // Ambil nomor urut terakhir untuk hari ini
        $lastPermohonan = static::whereDate('created_at', today())
                               ->orderBy('id', 'desc')
                               ->first();
        
        $nextNumber = 1;
        if ($lastPermohonan) {
            // Extract nomor urut dari nomor permohonan terakhir
            $lastNumber = (int)substr($lastPermohonan->nomor_permohonan, -4);
            $nextNumber = $lastNumber + 1;
        }
        
        return 'TRA' . $year . $month . $day . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private static function createNomorPermohonanWithTimestamp()
    {
        $timestamp = now()->format('YmdHis');
        $random = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        return 'TRA' . $timestamp . $random;
    }
}
