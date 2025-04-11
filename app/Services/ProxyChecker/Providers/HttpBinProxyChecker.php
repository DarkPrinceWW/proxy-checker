<?php

declare(strict_types=1);

namespace App\Services\ProxyChecker\Providers;

use App\Services\ProxyChecker\DTOs\ProxyCheckerResult;
use Illuminate\Http\Client\Response;

class HttpBinProxyChecker extends BaseProxyChecker
{
    private const string API_URL = 'http://httpbin.org/ip';

    protected function getApiUrl(): string
    {
        return self::API_URL;
    }

    protected function prepareResult(Response $response, string $type, float $duration): ProxyCheckerResult
    {
        return ProxyCheckerResult::from([
            'status' => true,
            'type' => strtoupper($type),
            'country' => null,
            'city' => null,
            'response_time' => $duration,
            'external_ip' => $response->json()['origin'] ?? null,
        ]);
    }
}
