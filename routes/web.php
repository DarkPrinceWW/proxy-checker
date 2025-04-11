<?php

declare(strict_types=1);

use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProxyController;
use Illuminate\Support\Facades\Route;

// Главная страница (проверка прокси)
Route::get('/', [ProxyController::class, 'index'])->name('home');
Route::post('/check', [ProxyController::class, 'store'])->name('check');

// История проверок
Route::get('/history', [HistoryController::class, 'index'])->name('history');
Route::get('/history/{checkSession}', [HistoryController::class, 'show'])->name('history.show');
