<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\CertificateController;

Route::get('/', function () {
    return view('welcome');
});

// Routes untuk download dengan security middleware
Route::middleware(['auth', 'secure.download'])->group(function () {
    Route::get('/download/dokumen/{permohonan}', [DownloadController::class, 'downloadDokumen'])
        ->name('download.dokumen');
    
    Route::get('/download/certificate/{hasilTera}', [DownloadController::class, 'downloadCertificate'])
        ->name('download.certificate');
    
    Route::get('/download/dokumen-multiple/{permohonan}', [DownloadController::class, 'downloadMultipleDokumen'])
        ->name('download.dokumen.multiple');
});

// Certificate routes
Route::middleware(['auth'])->group(function () {
    Route::get('/certificate/{hasilTera}/download', [CertificateController::class, 'generateCertificate'])
         ->name('certificate.download');
    
    Route::get('/certificate/{hasilTera}/preview', [CertificateController::class, 'previewCertificate'])
         ->name('certificate.preview');
    
    Route::post('/certificates/bulk-download', [CertificateController::class, 'generateBulkCertificates'])
         ->name('certificates.bulk-download');
});
