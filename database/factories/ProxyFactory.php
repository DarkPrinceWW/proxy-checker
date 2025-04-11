<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProxyStatusEnum;
use App\Enums\ProxyTypeEnum;
use App\Models\CheckSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proxy>
 */
class ProxyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'check_session_id' => CheckSession::factory(),
            'ip' => $this->faker->ipv4,
            'port' => $this->faker->numberBetween(1000, 9999),
            'status' => $this->faker->randomElement(ProxyStatusEnum::cases()),
            'type' => $this->faker->randomElement(ProxyTypeEnum::cases()),
        ];
    }
}
