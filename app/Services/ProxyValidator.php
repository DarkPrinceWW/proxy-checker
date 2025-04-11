<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ProxyValidatorInterface;

class ProxyValidator implements ProxyValidatorInterface
{
    /**
     * Валидирует формат прокси (ip:port).
     */
    public function isValidProxyFormat(string $proxy): bool
    {
        // Проверяем, что строка содержит ровно один двоеточие
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

    /**
     * Парсит и валидирует список прокси.
     *
     * @return array{valid: array<string>, invalid: array<string>, total: int}
     */
    public function parseAndValidateProxies(string $proxiesInput): array
    {
        $proxies = array_filter(array_map('trim', explode("\n", $proxiesInput)));
        $validProxies = [];
        $invalidProxies = [];
        $totalProxies = 0;

        foreach ($proxies as $proxy) {
            if ($this->isValidProxyFormat($proxy)) {
                $validProxies[] = $proxy;
                $totalProxies++;
            } else {
                $invalidProxies[] = $proxy;
            }
        }

        return [
            'valid' => $validProxies,
            'invalid' => $invalidProxies,
            'total' => $totalProxies,
        ];
    }
}
