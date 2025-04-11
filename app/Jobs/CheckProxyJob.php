<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\ProxyCheckService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckProxyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $proxy,
        private readonly int $checkSessionId,
    ) {}

    public function handle(ProxyCheckService $proxyCheckService): void
    {
        $proxyCheckService->checkProxy($this->proxy, $this->checkSessionId);
    }
}
