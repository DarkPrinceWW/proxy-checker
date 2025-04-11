<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CheckSession;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        return view('history.index', [
            'checkSessions' => CheckSession::with('proxies')->latest()->get(),
        ]);
    }

    public function show(CheckSession $checkSession): View
    {
        return view('history.show', [
            'checkSession' => $checkSession,
            'proxies' => $checkSession->proxies,
        ]);
    }
}
