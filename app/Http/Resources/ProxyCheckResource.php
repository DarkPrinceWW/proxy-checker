<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ProxyCheck;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProxyCheck
 */
class ProxyCheckResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'check_session_id' => $this->check_session_id,
            'ip' => $this->ip,
            'port' => $this->port,
            'status' => $this->status,
            'type' => $this->type,
            'country' => $this->country,
            'city' => $this->city,
            'response_time' => $this->response_time,
            'external_ip' => $this->external_ip,
            'error_count' => $this->error_count,
        ];
    }
}
