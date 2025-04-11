<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Contracts\ProxyCheckerContract;
use App\Models\CheckSession;
use App\Models\ProxyCheck;
use App\Services\ProxyChecker\DTOs\ProxyCheckerResult;
use App\Services\ProxyCheckService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ProxyCheckServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_proxy_successful_check(): void
    {
        $session = CheckSession::factory()->create(['total_proxies' => 1]);
        $proxyChecker = Mockery::mock(ProxyCheckerContract::class);

        $result = ProxyCheckerResult::from([
            'status' => true,
            'type' => 'SOCKS5', // Ожидаем SOCKS5, так как это первый тип в переборе
            'country' => 'USA',
            'city' => 'New York',
            'response_time' => 150.5,
            'external_ip' => '8.8.8.8',
        ]);

        $proxyChecker->shouldReceive('check')
            ->once()
            ->with('1.2.3.4', '8080') // Убрали третий параметр 'HTTP'
            ->andReturn($result);

        $service = new ProxyCheckService($proxyChecker);
        $service->checkProxy('1.2.3.4:8080', $session->id);

        $this->assertDatabaseHas('proxy_checks', [
            'check_session_id' => $session->id,
            'ip' => '1.2.3.4',
            'port' => '8080',
            'status' => true,
            'type' => 'SOCKS5', // Ожидаем SOCKS5
            'country' => 'USA',
            'city' => 'New York',
            'external_ip' => '8.8.8.8',
            'error_count' => 0,
        ]);

        // Проверяем response_time с допуском
        $proxyCheck = ProxyCheck::where('check_session_id', $session->id)->first();
        $this->assertTrue(
            $proxyCheck->response_time >= 149.5 && $proxyCheck->response_time <= 151.5,
            "Expected response_time to be around 150.5, but got {$proxyCheck->response_time}"
        );

        $this->assertDatabaseHas('check_sessions', [
            'id' => $session->id,
            'working_proxies' => 1,
        ]);

        $checkSession = CheckSession::find($session->id);
        $this->assertGreaterThanOrEqual(0, $checkSession->duration);
    }

    public function test_check_proxy_failed_after_max_retries(): void
    {
        $session = CheckSession::factory()->create(['total_proxies' => 1]);
        $proxyChecker = Mockery::mock(ProxyCheckerContract::class);

        $proxyChecker->shouldReceive('check')
            ->times(2)
            ->with('1.2.3.4', '8080') // Убрали третий параметр 'HTTP'
            ->andReturn(false);

        $service = new ProxyCheckService($proxyChecker);
        $service->checkProxy('1.2.3.4:8080', $session->id);

        $this->assertDatabaseHas('proxy_checks', [
            'check_session_id' => $session->id,
            'ip' => '1.2.3.4',
            'port' => '8080',
            'status' => false,
            'error_count' => 2,
        ]);

        $this->assertDatabaseHas('check_sessions', [
            'id' => $session->id,
            'working_proxies' => 0,
        ]);

        $checkSession = CheckSession::find($session->id);
        $this->assertGreaterThanOrEqual(0, $checkSession->duration);
    }
}
