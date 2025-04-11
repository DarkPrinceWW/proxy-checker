<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Contracts\ProxyCheckerContract;
use App\Dto\ProxyCheckerData;
use App\Enums\ProxyStatusEnum;
use App\Jobs\CheckProxyJob;
use App\Models\CheckSession;
use App\Models\Proxy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckProxyJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_proxy_to_valid(): void
    {
        $session = CheckSession::factory()->create();
        $proxy = Proxy::factory()->create([
            'check_session_id' => $session->id,
            'ip' => '192.168.1.1',
            'port' => 8080,
            'status' => ProxyStatusEnum::Pending,
        ]);

        $checker = $this->mock(ProxyCheckerContract::class);
        $checker->shouldReceive('check')
            ->with('192.168.1.1', 8080)
            ->andReturn(ProxyCheckerData::from([
                'type' => 'http',
                'country' => 'USA',
                'city' => 'New York',
                'response_time' => 201,
                'external_ip' => '172.16.0.1',
            ]));

        $job = new CheckProxyJob($proxy);
        $job->handle($checker);

        $this->assertDatabaseHas('proxies', [
            'id' => $proxy->id,
            'status' => ProxyStatusEnum::Valid->value,
            'type' => 'http',
            'country' => 'USA',
            'city' => 'New York',
            'response_time' => 201,
            'external_ip' => '172.16.0.1',
        ]);
    }

    public function test_updates_proxy_to_invalid(): void
    {
        $session = CheckSession::factory()->create();
        $proxy = Proxy::factory()->create([
            'check_session_id' => $session->id,
            'status' => ProxyStatusEnum::Pending,
        ]);

        $checker = $this->mock(ProxyCheckerContract::class);
        $checker->shouldReceive('check')->andReturn(null);

        $job = new CheckProxyJob($proxy);
        $job->handle($checker);

        $this->assertDatabaseHas('proxies', [
            'id' => $proxy->id,
            'status' => ProxyStatusEnum::Invalid->value,
        ]);
    }

    public function test_updates_session_duration_when_complete(): void
    {
        $session = CheckSession::factory()->create(['finished_at' => null]);
        $proxy = Proxy::factory()->create([
            'check_session_id' => $session->id,
            'status' => ProxyStatusEnum::Pending,
        ]);

        $checker = $this->mock(ProxyCheckerContract::class);
        $checker->shouldReceive('check')->andReturn(null);

        // Изменяем время для симуляции времени выполнения
        $this->travelTo(now()->addSeconds(10));

        $job = new CheckProxyJob($proxy);
        $job->handle($checker);

        $this->assertDatabaseHas('proxies', [
            'id' => $proxy->id,
            'status' => ProxyStatusEnum::Invalid->value,
        ]);

        $this->assertNotNull($session->fresh()->finished_at);
        $this->assertGreaterThan(0, $session->duration);
    }
}
