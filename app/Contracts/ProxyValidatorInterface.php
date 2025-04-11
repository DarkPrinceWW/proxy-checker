<?php

declare(strict_types=1);

namespace App\Contracts;

interface ProxyValidatorInterface
{
    public function isValidProxyFormat(string $proxy): bool;

    public function parseAndValidateProxies(string $proxiesInput): array;
}
