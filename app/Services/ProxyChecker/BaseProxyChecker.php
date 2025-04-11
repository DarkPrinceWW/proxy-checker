<?php

declare(strict_types=1);

namespace App\Services\ProxyChecker;

use App\Contracts\ProxyCheckerContract;
use App\Dto\ProxyCheckerData;
use App\Enums\ProxyTypeEnum;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

abstract class BaseProxyChecker implements ProxyCheckerContract
{
    private const int DEFAULT_TIMEOUT = 5;

    protected float|int $requestTimeout;

    /**
     * @param  array{timeout?: int|float}  $config
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $config = [])
    {
        $timeout = Arr::get($config, 'timeout', self::DEFAULT_TIMEOUT);
        if (!is_numeric($timeout) || $timeout <= 0) {
            throw new InvalidArgumentException('Timeout must be a positive number');
        }
        $this->requestTimeout = $timeout;
    }

    /**
     * Проверяет прокси на работоспособность по всем типам асинхронно.
     */
    public function check(string $ip, int $port): ?ProxyCheckerData
    {
        $responses = Http::pool(function(Pool $pool) use ($ip, $port) {
            $requests = [];

            foreach (ProxyTypeEnum::cases() as $type) {
                $proxyUrl = sprintf('%s://%s:%s', $type->value, $ip, $port);
                $requests[] = $pool
                    ->as($type->value)
                    ->timeout($this->requestTimeout)
                    ->withOptions(['proxy' => $proxyUrl])
                    ->get($this->getApiUrl());
            }

            return $requests;
        });

        foreach ($responses as $type => $response) {
            if ($response instanceof Response) {
                if ($response->successful()) {
                    return $this->prepareResult($response, ProxyTypeEnum::from($type));
                }
                Log::debug("Proxy check failed for $type://$ip:$port", [
                    'status' => $response->status(),
                    'error' => $response->reason(),
                ]);
            } else {
                Log::warning("Proxy check error for $type://$ip:$port", [
                    'error' => $response->getMessage() ?? 'Unknown error',
                ]);
            }
        }

        return null;
    }

    /**
     * Возвращает URL API для проверки прокси.
     */
    abstract protected function getApiUrl(): string;

    /**
     * Подготавливает результат проверки прокси.
     */
    abstract protected function prepareResult(Response $response, ProxyTypeEnum $type): ProxyCheckerData;
}
