<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('home');
})->name('home');

// File operations
Route::get('/download/{pinCode}', [FileController::class, 'download'])->name('file.download');
Route::delete('/files/{id}', [FileController::class, 'delete'])->name('file.delete');

// Payment routes
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');

// Admin routes (protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});
