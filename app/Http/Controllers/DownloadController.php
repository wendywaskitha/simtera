<?php

namespace App\Http\Controllers;

use App\Models\HasilTera;
use App\Models\PermohonanTera;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Storage;

class DownloadController extends Controller
{
    public function downloadCertificate(HasilTera $hasilTera)
    {
        // Validasi akses
        if ($hasilTera->hasil !== 'Lulus' || !$hasilTera->nomor_sertifikat) {
            abort(404, 'Sertifikat tidak tersedia');
        }

        // Generate PDF jika belum ada
        $pdfPath = $this->generateCertificatePDF($hasilTera);
        
        if (!Storage::exists($pdfPath)) {
            abort(404, 'File sertifikat tidak ditemukan');
        }

        $fileName = "Sertifikat_Tera_{$hasilTera->nomor_sertifikat}.pdf";
        
        return Storage::download($pdfPath, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function downloadDokumen(PermohonanTera $permohonan)
    {
        if (empty($permohonan->dokumen_pendukung)) {
            abort(404, 'Dokumen tidak tersedia');
        }

        $dokumenPath = is_array($permohonan->dokumen_pendukung) 
            ? $permohonan->dokumen_pendukung[0] 
            : $permohonan->dokumen_pendukung;

        if (!Storage::exists($dokumenPath)) {
            abort(404, 'File dokumen tidak ditemukan');
        }

        $fileName = "Dokumen_Permohonan_{$permohonan->nomor_permohonan}." . 
                   pathinfo($dokumenPath, PATHINFO_EXTENSION);

        return Storage::download($dokumenPath, $fileName);
    }

    private function generateCertificatePDF(HasilTera $hasilTera)
    {
        $certificatesPath = 'certificates';
        $fileName = "sertifikat_{$hasilTera->nomor_sertifikat}.pdf";
        $fullPath = "{$certificatesPath}/{$fileName}";

        // Cek apakah file sudah ada
        if (Storage::exists($fullPath)) {
            return $fullPath;
        }

        // Buat direktori jika belum ada
        if (!Storage::exists($certificatesPath)) {
            Storage::makeDirectory($certificatesPath);
        }

        // Generate PDF menggunakan TCPDF atau library lain
        $pdf = $this->createCertificatePDF($hasilTera);
        
        // Simpan PDF
        Storage::put($fullPath, $pdf->Output('', 'S'));
        
        return $fullPath;
    }

    private function createCertificatePDF(HasilTera $hasilTera)
    {
        // Implementasi menggunakan TCPDF
        $pdf = new \TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 16);
        
        // Header sertifikat
        $pdf->Cell(0, 15, 'SERTIFIKAT TERA', 0, 1, 'C');
        $pdf->Ln(10);
        
        // Logo dan kop surat
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'UPTD METROLOGI LEGAL', 0, 1, 'C');
        $pdf->Cell(0, 8, 'KABUPATEN MUNA BARAT', 0, 1, 'C');
        $pdf->Ln(10);
        
        // Content sertifikat
        $pdf->SetFont('helvetica', '', 10);
        
        $content = "
        <table cellpadding='5'>
            <tr>
                <td width='30%'>Nomor Sertifikat</td>
                <td width='5%'>:</td>
                <td width='65%'>{$hasilTera->nomor_sertifikat}</td>
            </tr>
            <tr>
                <td>Nama Pemilik</td>
                <td>:</td>
                <td>{$hasilTera->uttp->nama_pemilik}</td>
            </tr>
            <tr>
                <td>Jenis UTTP</td>
                <td>:</td>
                <td>{$hasilTera->uttp->jenisUttp->nama}</td>
            </tr>
            <tr>
                <td>Merk/Tipe</td>
                <td>:</td>
                <td>{$hasilTera->uttp->merk} / {$hasilTera->uttp->tipe}</td>
            </tr>
            <tr>
                <td>Nomor Seri</td>
                <td>:</td>
                <td>{$hasilTera->uttp->nomor_seri}</td>
            </tr>
            <tr>
                <td>Kapasitas Maksimum</td>
                <td>:</td>
                <td>{$hasilTera->uttp->kapasitas_maksimum} {$hasilTera->uttp->jenisUttp->satuan}</td>
            </tr>
            <tr>
                <td>Tanggal Tera</td>
                <td>:</td>
                <td>{$hasilTera->tanggal_tera->format('d F Y')}</td>
            </tr>
            <tr>
                <td>Tanggal Expired</td>
                <td>:</td>
                <td>{$hasilTera->tanggal_expired->format('d F Y')}</td>
            </tr>
            <tr>
                <td>Petugas Tera</td>
                <td>:</td>
                <td>{$hasilTera->petugas_tera}</td>
            </tr>
        </table>
        ";
        
        $pdf->writeHTML($content, true, false, true, false, '');
        
        // Footer
        $pdf->Ln(20);
        $pdf->Cell(0, 8, 'Raha, ' . $hasilTera->tanggal_tera->format('d F Y'), 0, 1, 'R');
        $pdf->Cell(0, 8, 'Kepala UPTD Metrologi Legal', 0, 1, 'R');
        $pdf->Ln(20);
        $pdf->Cell(0, 8, '_________________________', 0, 1, 'R');
        
        return $pdf;
    }
}
