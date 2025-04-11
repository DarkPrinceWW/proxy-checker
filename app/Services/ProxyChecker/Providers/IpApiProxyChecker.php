<?php

declare(strict_types=1);

namespace App\Services\ProxyChecker\Providers;

use App\Services\ProxyChecker\DTOs\ProxyCheckerResult;
use Illuminate\Http\Client\Response;

class IpApiProxyChecker extends BaseProxyChecker
{
    private const string API_URL = 'http://ip-api.com/json';

    protected function getApiUrl(): string
    {
        return self::API_URL;
    }

    protected function prepareResult(Response $response, string $type, float $duration): ProxyCheckerResult
    {
        $data = $response->json();

        return ProxyCheckerResult::from([
            'status' => true,
            'type' => strtoupper($type),
            'country' => $data['country'] ?? null,
            'city' => $data['city'] ?? null,
            'response_time' => $duration,
            'external_ip' => $data['query'] ?? null,
        ]);
    }
}
