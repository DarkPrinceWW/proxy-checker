<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CheckSession;
use App\Services\CheckSessionService;
use Illuminate\Http\JsonResponse;

class CheckSessionController extends Controller
{
    public function checkStatus(CheckSession $checkSession, CheckSessionService $checkSessionService): JsonResponse
    {
        return response()->json($checkSessionService->getCheckStatus($checkSession));
    }
}
