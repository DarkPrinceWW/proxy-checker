<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProxyResource;
use App\Http\Resources\StatusResource;
use App\Models\CheckSession;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class HistoryController extends Controller
{
    public function proxies(CheckSession $checkSession): ResourceCollection
    {
        return ProxyResource::collection($checkSession->proxies);
    }

    public function status(CheckSession $checkSession): JsonResource
    {
        return StatusResource::make($checkSession->load('proxies'));
    }
}
