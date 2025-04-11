<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ProxyValidatorContract;
use App\Dto\ProxyValidationData;

class ProxyValidator implements ProxyValidatorContract
{
    public function validate(array $proxies): ProxyValidationData
    {
        $result = collect($proxies)
            ->map(fn(string $proxy) => trim($proxy))
            ->filter()
            ->map(fn(string $proxy) => [
                'proxy' => $proxy,
                'valid' => $this->isValidProxyFormat($proxy),
            ]);

        return ProxyValidationData::from([
            'valid' => $result->where('valid', true)->pluck('proxy'),
            'invalid' => $result->where('valid', false)->pluck('proxy'),
        ]);
    }

    private function isValidProxyFormat(string $proxy): bool
    {
        // Проверяем, что строка содержит одно двоеточие
        if (substr_count($proxy, ':') !== 1) {
            return false;
        }

        [$ip, $port] = explode(':', $proxy);

        // Проверяем, что IP-адрес валидный
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        // Проверяем, что порт — это число от 1 до 65535
        if (!is_numeric($port) || (int)$port < 1 || (int)$port > 65535) {
            return false;
        }

        return true;
    }
}
