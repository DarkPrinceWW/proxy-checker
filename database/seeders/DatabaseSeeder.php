<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ProxyStatusEnum;
use App\Models\CheckSession;
use App\Models\Proxy;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        CheckSession::factory(10)
            ->create()
            ->each(function(CheckSession $checkSession) {
                Proxy::factory(rand(1, 20))->create([
                    'check_session_id' => $checkSession->id,
                    'status' => fake()->randomElement([
                        ProxyStatusEnum::Valid,
                        ProxyStatusEnum::Invalid,
                    ]),
                ]);
            });
    }
}
