<?php

use App\Http\Controllers\PdfCompressionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PdfCompressionController::class, 'index'])->name('pdf.index');
Route::post('/compress', [PdfCompressionController::class, 'compress'])->name('pdf.compress');
Route::get('/download/{token}', [PdfCompressionController::class, 'download'])->name('pdf.download');
Route::get('/download-batch/{batchToken}', [PdfCompressionController::class, 'downloadBatch'])->name('pdf.downloadBatch');
