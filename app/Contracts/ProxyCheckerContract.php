<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Services\ProxyChecker\DTOs\ProxyCheckerResult;

interface ProxyCheckerContract
{
    public function check(string $ip, string $port): ProxyCheckerResult|false;
}
