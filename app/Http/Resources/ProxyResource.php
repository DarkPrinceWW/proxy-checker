<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Proxy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Proxy
 */
class ProxyResource extends JsonResource
{
    public function toArray(Request $request): array
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
        ];
    }
}
