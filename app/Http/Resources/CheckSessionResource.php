<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\CheckSession;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CheckSession
 */
class CheckSessionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'total_proxies' => $this->total_proxies,
            'working_proxies' => $this->working_proxies,
            'duration' => $this->duration,
            'created_at' => $this->created_at,
        ];
    }
}
