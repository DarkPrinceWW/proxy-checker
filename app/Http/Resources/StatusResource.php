<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\CheckSession;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CheckSession
 */
class StatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $totalProxies = $this->total_proxies;
        $checkedProxies = $this->checked_proxies;
        $progress = $totalProxies > 0 ? ($checkedProxies / $totalProxies) * 100 : 0;

        return [
            'progress' => $progress,
            'checked' => $checkedProxies,
            'working' => $this->working_proxies,
            'total' => $totalProxies,
            'duration' => $this->duration,
            'completed' => $checkedProxies === $totalProxies,
        ];
    }
}
