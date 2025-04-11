<?php

declare(strict_types=1);

use App\Http\Controllers\CheckSessionController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProxyCheckController;
use Illuminate\Support\Facades\Route;

// Главная страница (проверка прокси)
Route::get('/', [ProxyCheckController::class, 'index'])->name('proxies.index');
Route::post('/check', [ProxyCheckController::class, 'check'])->name('proxies.check');

// История проверок
Route::get('/history', [HistoryController::class, 'index'])->name('history');
Route::get('/history/{checkSession}', [HistoryController::class, 'show'])->name('history.show');
Route::get('/history/{checkSession}/results', [HistoryController::class, 'getResults'])->name('history.results');

// Получение статуса проверки
Route::get('/check-session/{checkSession}', [CheckSessionController::class, 'checkStatus'])->name('check.status');
