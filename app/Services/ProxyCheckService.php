<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ProxyCheckerContract;
use App\Models\CheckSession;
use App\Models\ProxyCheck;

class ProxyCheckService
{
    private const int MAX_RETRIES = 2;

    public function __construct(private ProxyCheckerContract $proxyChecker) {}

    public function checkProxy(string $proxy, int $checkSessionId): void
    {
        [$ip, $port] = explode(':', $proxy);

        $proxyCheck = ProxyCheck::create([
            'check_session_id' => $checkSessionId,
            'status' => false,
            'type' => null,
            'ip' => $ip,
            'port' => $port,
            'error_count' => 0,
        ]);

        $retryCount = 0;
        $success = false;

        while ($retryCount < self::MAX_RETRIES && !$success) {
            // Передаем тип прокси в метод check
            $result = $this->proxyChecker->check($ip, $port);

            if ($result === false) {
                $proxyCheck->increment('error_count');
                $retryCount++;

                continue;
            }

            $proxyCheck->update([
                'type' => $result->type,
                'status' => $result->status,
                'country' => $result->country,
                'city' => $result->city,
                'response_time' => $result->responseTime,
                'external_ip' => $result->externalIp,
            ]);
            $success = true;
        }

        // Обновляем сессию после проверки
        $this->updateCheckSession($checkSessionId);
    }

    private function updateCheckSession(int $checkSessionId): void
    {
        $checkSession = CheckSession::findOrFail($checkSessionId);
        $proxyChecks = $checkSession->proxyChecks()->get();
        $totalProxies = $checkSession->total_proxies;
        $checkedProxies = $proxyChecks->count();
        $workingProxies = $proxyChecks->where('status', true)->count();

        if ($checkedProxies === $totalProxies) {
            $duration = now()->diffInSeconds($checkSession->created_at);
            $duration = abs($duration);

            $checkSession->update([
                'working_proxies' => $workingProxies,
                'duration' => $duration,
            ]);
        }
    }
}
