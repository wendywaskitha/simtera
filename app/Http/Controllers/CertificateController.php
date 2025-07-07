<?php

namespace App\Http\Controllers;

use App\Models\HasilTera;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    /**
     * Generate PDF certificate for hasil tera
     */
    public function generateCertificate(HasilTera $hasilTera)
    {
        // Validasi bahwa hasil tera adalah lulus
        if ($hasilTera->hasil !== 'Lulus') {
            abort(404, 'Sertifikat hanya tersedia untuk hasil tera yang lulus');
        }
        
        // Load relasi yang diperlukan
        $hasilTera->load(['uttp.jenisUttp', 'uttp.desa.kecamatan', 'permohonanTera']);
        
        // Generate PDF
        $pdf = Pdf::loadView('pdf.sertifikat-tera', compact('hasilTera'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'dpi' => 150,
                      'defaultFont' => 'sans-serif',
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true,
                  ]);
        
        // Nama file
        $filename = 'Sertifikat_Tera_' . $hasilTera->nomor_sertifikat . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Preview certificate in browser
     */
    public function previewCertificate(HasilTera $hasilTera)
    {
        if ($hasilTera->hasil !== 'Lulus') {
            abort(404, 'Sertifikat hanya tersedia untuk hasil tera yang lulus');
        }
        
        $hasilTera->load(['uttp.jenisUttp', 'uttp.desa.kecamatan', 'permohonanTera']);
        
        $pdf = Pdf::loadView('pdf.sertifikat-tera', compact('hasilTera'))
                  ->setPaper('a4', 'portrait');
        
        return $pdf->stream('preview_sertifikat.pdf');
    }
    
    /**
     * Generate bulk certificates
     */
    public function generateBulkCertificates(Request $request)
    {
        $hasilTeraIds = $request->input('hasil_tera_ids', []);
        
        if (empty($hasilTeraIds)) {
            return back()->with('error', 'Pilih minimal satu hasil tera untuk generate sertifikat');
        }
        
        $hasilTeras = HasilTera::whereIn('id', $hasilTeraIds)
                              ->where('hasil', 'Lulus')
                              ->with(['uttp.jenisUttp', 'uttp.desa.kecamatan'])
                              ->get();
        
        if ($hasilTeras->isEmpty()) {
            return back()->with('error', 'Tidak ada hasil tera yang valid untuk generate sertifikat');
        }
        
        // Create ZIP file untuk multiple certificates
        $zip = new \ZipArchive();
        $zipFileName = 'Sertifikat_Tera_Bulk_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        // Ensure temp directory exists
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }
        
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($hasilTeras as $hasilTera) {
                $pdf = Pdf::loadView('pdf.sertifikat-tera', compact('hasilTera'))
                          ->setPaper('a4', 'portrait');
                
                $pdfContent = $pdf->output();
                $pdfFileName = 'Sertifikat_' . $hasilTera->nomor_sertifikat . '.pdf';
                
                $zip->addFromString($pdfFileName, $pdfContent);
            }
            $zip->close();
            
            return response()->download($zipPath)->deleteFileAfterSend(true);
        }
        
        return back()->with('error', 'Gagal membuat file ZIP');
    }
}
