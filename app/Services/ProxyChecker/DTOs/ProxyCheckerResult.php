<?php

declare(strict_types=1);

namespace App\Services\ProxyChecker\DTOs;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class ProxyCheckerResult extends Data
{
    public function __construct(
        public bool $status,
        public string $type,
        public ?string $country,
        public ?string $city,
        public float $responseTime,
        public ?string $externalIp,
    ) {}
}
