<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notifikasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'pesan',
        'tipe',
        'penerima',
        'status',
        'tanggal_kirim',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'tanggal_kirim' => 'datetime',
        'metadata' => 'array',
    ];

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'Sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'Failed');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'Sent' => 'success',
            'Failed' => 'danger',
            'Pending' => 'warning',
            default => 'secondary'
        };
    }

    public function getTipeBadgeAttribute()
    {
        return match($this->tipe) {
            'Email' => 'primary',
            'SMS' => 'info',
            'WhatsApp' => 'success',
            'System' => 'secondary',
            default => 'secondary'
        };
    }

    // Methods
    public function markAsSent()
    {
        $this->update([
            'status' => 'Sent',
            'tanggal_kirim' => Carbon::now(),
            'error_message' => null,
        ]);
    }

    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'Failed',
            'error_message' => $errorMessage,
        ]);
    }

    // Static methods untuk membuat notifikasi
    public static function createNotifikasi($judul, $pesan, $tipe, $penerima, $metadata = null)
    {
        return static::create([
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'penerima' => $penerima,
            'metadata' => $metadata,
            'status' => 'Pending',
        ]);
    }

    public static function notifikasiPermohonanBaru($permohonan)
    {
        return static::createNotifikasi(
            'Permohonan Tera Baru',
            "Permohonan tera baru dengan nomor {$permohonan->nomor_permohonan} telah diterima.",
            'System',
            'admin',
            ['permohonan_id' => $permohonan->id]
        );
    }

    public static function notifikasiReminderExpired($uttp)
    {
        return static::createNotifikasi(
            'Reminder Tera Ulang',
            "UTTP {$uttp->kode_uttp} milik {$uttp->nama_pemilik} akan expired dalam 30 hari.",
            'Email',
            $uttp->telepon_pemilik ?? 'admin',
            ['uttp_id' => $uttp->id]
        );
    }
}
