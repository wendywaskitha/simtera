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
                // Load relasi UTTP dengan jenisUTTP untuk mendapatkan kode
                $model->loadMissing('uttp.jenisUttp');
                $kodeJenis = $model->uttp?->jenisUttp?->kode ?? 'TRA';
                $model->nomor_permohonan = static::generateNomorPermohonan($kodeJenis);
            }
        });
    }

    // Method generate nomor yang thread-safe dengan kode jenis UTTP
    private static function generateNomorPermohonan($kodeJenis)
    {
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $attempt++;
            $nomor = static::createNomorPermohonan($kodeJenis);

            // Cek apakah nomor sudah ada
            $exists = static::where('nomor_permohonan', $nomor)->exists();

            if (!$exists) {
                return $nomor;
            }

            // Jika sudah ada, tunggu sebentar dan coba lagi
            usleep(100000); // 100ms

        } while ($attempt < $maxAttempts);

        // Jika masih gagal, gunakan timestamp untuk uniqueness
        return static::createNomorPermohonanWithTimestamp($kodeJenis);
    }

    private static function createNomorPermohonan($kodeJenis)
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        // Pastikan kode jenis dalam huruf kapital
        $prefix = strtoupper($kodeJenis);

        // Ambil nomor urut terakhir untuk hari ini dengan prefix yang sama
        $lastPermohonan = static::whereDate('created_at', today())
                               ->where('nomor_permohonan', 'like', "$prefix%")
                               ->orderBy('id', 'desc')
                               ->first();

        $nextNumber = 1;
        if ($lastPermohonan) {
            // Extract nomor urut dari nomor permohonan terakhir (4 digit terakhir)
            $lastNumber = (int)substr($lastPermohonan->nomor_permohonan, -4);
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . $year . $month . $day . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private static function createNomorPermohonanWithTimestamp($kodeJenis)
    {
        $timestamp = now()->format('YmdHis');
        $random = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $prefix = strtoupper($kodeJenis);

        return $prefix . $timestamp . $random;
    }
}
