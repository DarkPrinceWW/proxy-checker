<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\ProxyCheckerContract;
use App\Enums\ProxyStatusEnum;
use App\Models\CheckSession;
use App\Models\Proxy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckProxyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly Proxy $proxy
    ) {}

    public function handle(ProxyCheckerContract $proxyChecker): void
    {
        $result = $proxyChecker->check($this->proxy->ip, $this->proxy->port);

        if ($result !== null) {
            $this->proxy->update([
                'status' => ProxyStatusEnum::Valid,
                ...$result->all(),
            ]);
        } else {
            $this->proxy->update(['status' => ProxyStatusEnum::Invalid]);
        }

        $this->updateCheckSession($this->proxy->checkSession);
    }

    private function updateCheckSession(CheckSession $checkSession): void
    {
        // Завершаем сессию, если все проверки завершены
        if ($checkSession->checked_proxies === $checkSession->total_proxies) {
            $checkSession->update(['finished_at' => now()]);
        }
    }
}
