<?php

declare(strict_types=1);

namespace App\Services\ProxyChecker;

use App\Dto\ProxyCheckerData;
use App\Enums\ProxyTypeEnum;
use Illuminate\Http\Client\Response;

class IpApiProxyChecker extends BaseProxyChecker
{
    private const string API_URL = 'http://ip-api.com/json';

    protected function getApiUrl(): string
    {
        return self::API_URL;
    }

    protected function prepareResult(Response $response, ProxyTypeEnum $type): ProxyCheckerData
    {
        $data = $response->json();
        $duration = $response->handlerStats()['total_time'] * 1000;

        return ProxyCheckerData::from([
            'type' => $type,
            'country' => $data['country'] ?? null,
            'city' => $data['city'] ?? null,
            'response_time' => $duration,
            'external_ip' => $data['query'] ?? null,
        ]);
    }
}
