<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\CheckProxyJob;
use App\Models\CheckSession;

class CheckSessionService
{
    public function createSession(int $totalProxies): CheckSession
    {
        return CheckSession::create([
            'total_proxies' => $totalProxies,
            'working_proxies' => 0,
            'duration' => 0,
        ]);
    }

    public function startProxyCheck(array $proxies, int $checkSessionId): void
    {
        foreach ($proxies as $proxy) {
            CheckProxyJob::dispatch($proxy, $checkSessionId);
        }
    }

    public function getCheckStatus(CheckSession $checkSession): array
    {
        $checkedProxies = $checkSession->proxyChecks()->count();
        $totalProxies = $checkSession->total_proxies;
        $progress = $totalProxies > 0 ? ($checkedProxies / $totalProxies) * 100 : 0;

        return [
            'progress' => $progress,
            'checked' => $checkedProxies,
            'total' => $totalProxies,
            'duration' => $checkSession->duration,
            'completed' => $checkedProxies === $totalProxies,
        ];
    }
}
