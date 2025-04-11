<?php

declare(strict_types=1);

namespace App\Services\ProxyChecker\Providers;

use App\Contracts\ProxyCheckerContract;
use App\Services\ProxyChecker\DTOs\ProxyCheckerResult;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

abstract class BaseProxyChecker implements ProxyCheckerContract
{
    private int|float $timeout;

    public function __construct(array $config = [])
    {
        $this->timeout = Arr::get($config, 'timeout', 5);
    }

    public function check(string $ip, string $port): ProxyCheckerResult|false
    {
        foreach (['socks5', 'socks4', 'http'] as $type) {
            $proxyUrl = "$type://$ip:$port";

            try {
                $start = microtime(true);
                $response = Http::timeout($this->timeout)
                    ->withOptions(['proxy' => $proxyUrl])
                    ->get($this->getApiUrl());

                if ($response->successful()) {
                    $duration = (microtime(true) - $start) * 1000;

                    return $this->prepareResult($response, $type, $duration);
                }
            } catch (Exception) {
                continue;
            }
        }

        return false;
    }

    abstract protected function getApiUrl(): string;

    abstract protected function prepareResult(Response $response, string $type, float $duration): ProxyCheckerResult;
}
