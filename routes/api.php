<?php

declare(strict_types=1);

use App\Http\Controllers\Api\HistoryController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function() {
    Route::get('/history/{checkSession}/proxies', [HistoryController::class, 'proxies'])->name('history.proxies');
    Route::get('/history/{checkSession}/status', [HistoryController::class, 'status'])->name('history.status');
});
