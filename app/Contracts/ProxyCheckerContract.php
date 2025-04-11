<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Dto\ProxyCheckerData;

interface ProxyCheckerContract
{
    /**
     * Проверяет прокси на работоспособность.
     */
    public function check(string $ip, int $port): ?ProxyCheckerData;
}
