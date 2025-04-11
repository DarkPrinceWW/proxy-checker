<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Dto\ProxyValidationData;

interface ProxyValidatorContract
{
    /**
     * Валидирует формат списка прокси (ip:port).
     */
    public function validate(array $proxies): ProxyValidationData;
}
