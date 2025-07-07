<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PermohonanTera;

return new class extends Migration
{
    public function up(): void
    {
        // Cari dan fix duplicate nomor permohonan
        $duplicates = PermohonanTera::select('nomor_permohonan')
                                   ->groupBy('nomor_permohonan')
                                   ->havingRaw('COUNT(*) > 1')
                                   ->pluck('nomor_permohonan');
        
        foreach ($duplicates as $nomorDuplicate) {
            $records = PermohonanTera::where('nomor_permohonan', $nomorDuplicate)
                                    ->orderBy('id')
                                    ->get();
            
            // Skip yang pertama, update yang lainnya
            foreach ($records->skip(1) as $index => $record) {
                $newNomor = $nomorDuplicate . '_' . ($index + 2);
                $record->update(['nomor_permohonan' => $newNomor]);
            }
        }
    }

    public function down(): void
    {
        // Tidak perlu rollback untuk fix data
    }
};
