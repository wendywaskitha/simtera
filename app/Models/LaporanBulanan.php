<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LaporanBulanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'bulan',
        'total_uttp_terdaftar',
        'total_tera_dilakukan',
        'total_tera_lulus',
        'total_tera_tidak_lulus',
        'total_permohonan',
        'detail_per_jenis',
        'detail_per_lokasi',
    ];

    protected $casts = [
        'detail_per_jenis' => 'array',
        'detail_per_lokasi' => 'array',
    ];

    // Scopes
    public function scopeByTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    public function scopeByBulan($query, $bulan)
    {
        return $query->where('bulan', $bulan);
    }

    public function scopeByPeriode($query, $tahun, $bulan)
    {
        return $query->where('tahun', $tahun)->where('bulan', $bulan);
    }

    // Accessors
    public function getNamaBulanAttribute()
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $namaBulan[$this->bulan] ?? '';
    }

    public function getPeriodeLengkapAttribute()
    {
        return $this->nama_bulan . ' ' . $this->tahun;
    }

    public function getPersentaseLulusAttribute()
    {
        if ($this->total_tera_dilakukan == 0) {
            return 0;
        }
        
        return round(($this->total_tera_lulus / $this->total_tera_dilakukan) * 100, 2);
    }

    // Static methods untuk generate laporan
    public static function generateLaporan($tahun, $bulan)
    {
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        // Hitung statistik
        $totalUttpTerdaftar = UTTP::whereYear('created_at', '<=', $tahun)
                                  ->whereMonth('created_at', '<=', $bulan)
                                  ->count();

        $hasilTeraBulanIni = HasilTera::whereBetween('tanggal_tera', [$startDate, $endDate]);
        
        $totalTeraDilakukan = $hasilTeraBulanIni->count();
        $totalTeraLulus = $hasilTeraBulanIni->where('hasil', 'Lulus')->count();
        $totalTeraNotLulus = $totalTeraDilakukan - $totalTeraLulus;

        $totalPermohonan = PermohonanTera::whereBetween('tanggal_permohonan', [$startDate, $endDate])
                                        ->count();

        // Detail per jenis UTTP
        $detailPerJenis = HasilTera::whereBetween('tanggal_tera', [$startDate, $endDate])
                                  ->join('uttps', 'hasil_teras.uttp_id', '=', 'uttps.id')
                                  ->join('jenis_uttps', 'uttps.jenis_uttp_id', '=', 'jenis_uttps.id')
                                  ->selectRaw('jenis_uttps.nama, COUNT(*) as total, 
                                             SUM(CASE WHEN hasil_teras.hasil = "Lulus" THEN 1 ELSE 0 END) as lulus')
                                  ->groupBy('jenis_uttps.id', 'jenis_uttps.nama')
                                  ->get()
                                  ->toArray();

        // Detail per lokasi (kecamatan)
        $detailPerLokasi = HasilTera::whereBetween('tanggal_tera', [$startDate, $endDate])
                                   ->join('uttps', 'hasil_teras.uttp_id', '=', 'uttps.id')
                                   ->join('desas', 'uttps.desa_id', '=', 'desas.id')
                                   ->join('kecamatans', 'desas.kecamatan_id', '=', 'kecamatans.id')
                                   ->selectRaw('kecamatans.nama, COUNT(*) as total,
                                              SUM(CASE WHEN hasil_teras.hasil = "Lulus" THEN 1 ELSE 0 END) as lulus')
                                   ->groupBy('kecamatans.id', 'kecamatans.nama')
                                   ->get()
                                   ->toArray();

        return static::updateOrCreate(
            ['tahun' => $tahun, 'bulan' => $bulan],
            [
                'total_uttp_terdaftar' => $totalUttpTerdaftar,
                'total_tera_dilakukan' => $totalTeraDilakukan,
                'total_tera_lulus' => $totalTeraLulus,
                'total_tera_tidak_lulus' => $totalTeraNotLulus,
                'total_permohonan' => $totalPermohonan,
                'detail_per_jenis' => $detailPerJenis,
                'detail_per_lokasi' => $detailPerLokasi,
            ]
        );
    }
}
