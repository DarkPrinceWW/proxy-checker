<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class ProxyValidationData extends Data
{
    public function __construct(
        /** @var Collection<int, string> */
        public Collection $valid,
        /** @var Collection<int, string> */
        public Collection $invalid,
    ) {}
}
