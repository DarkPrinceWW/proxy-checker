<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\CheckSessionResource;
use App\Http\Resources\ProxyCheckResource;
use App\Models\CheckSession;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        return view('history', [
            'checkSessions' => CheckSessionResource::collection(CheckSession::latest()->get()),
        ]);
    }

    public function show(CheckSession $checkSession): View
    {
        return view('show', [
            'checkSession' => CheckSessionResource::make($checkSession),
            'proxyChecks' => ProxyCheckResource::collection($checkSession->proxyChecks),
        ]);
    }

    public function getResults(CheckSession $checkSession): ResourceCollection
    {
        return ProxyCheckResource::collection($checkSession->proxyChecks);
    }
}
