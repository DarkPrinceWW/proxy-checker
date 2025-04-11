<?php

declare(strict_types=1);

namespace App\Services\ProxyChecker;

use App\Dto\ProxyCheckerData;
use App\Enums\ProxyTypeEnum;
use Illuminate\Http\Client\Response;

class HttpBinProxyChecker extends BaseProxyChecker
{
    private const string API_URL = 'http://httpbin.org/ip';

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
            'country' => null,
            'city' => null,
            'response_time' => $duration,
            'external_ip' => $data['origin'] ?? null,
        ]);
    }
}
