<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartPdfController;
use App\Filament\Pages\Report;
use App\Http\Controllers\ChartController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Filament\Pages\Graphic;

Route::get('/', function () {
    return view('welcome');
});

// Rutas para guardar imágenes y exportar PDF
// Route::post('/graficos/save-chart-images', [ChartPdfController::class, 'saveChartImages'])
//     ->name('graficos.saveChartImages')
//     ->middleware(['web']);

// Route::get('/graficos/export-pdf', [ChartPdfController::class, 'exportPdf'])
//     ->name('graficos.exportPdf')
//     ->middleware(['web']);