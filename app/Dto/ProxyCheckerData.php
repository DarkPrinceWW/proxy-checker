<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enums\ProxyTypeEnum;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class ProxyCheckerData extends Data
{
    public function __construct(
        public ProxyTypeEnum $type,
        public ?string $country,
        public ?string $city,
        public float $responseTime,
        public ?string $externalIp,
    ) {}
}
