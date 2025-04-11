<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\ProxyStatusEnum;
use App\Models\CheckSession;
use App\Models\Proxy;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculates_total_proxies(): void
    {
        $session = CheckSession::factory()->create();
        Proxy::factory(5)->create(['check_session_id' => $session->id]);

        $this->assertEquals(5, $session->total_proxies);
    }

    public function test_calculates_checked_proxies(): void
    {
        $session = CheckSession::factory()->create();
        Proxy::factory(3)->create([
            'check_session_id' => $session->id,
            'status' => ProxyStatusEnum::Valid,
        ]);
        Proxy::factory(2)->create([
            'check_session_id' => $session->id,
            'status' => ProxyStatusEnum::Pending,
        ]);

        $this->assertEquals(3, $session->checked_proxies);
    }

    public function test_calculates_working_proxies(): void
    {
        $session = CheckSession::factory()->create();
        Proxy::factory(2)->create([
            'check_session_id' => $session->id,
            'status' => ProxyStatusEnum::Valid,
        ]);
        Proxy::factory(3)->create([
            'check_session_id' => $session->id,
            'status' => ProxyStatusEnum::Invalid,
        ]);

        $this->assertEquals(2, $session->working_proxies);
    }

    public function test_calculates_duration_with_null_finished_at()
    {
        Carbon::setTestNow(now());

        $session = CheckSession::factory()->create([
            'created_at' => now()->subSeconds(100),
            'finished_at' => null,
        ]);

        $this->assertEquals(100, $session->duration);

        // Симулируем изменение времени
        Carbon::setTestNow(now()->addSeconds(50));
        $this->assertEquals(150, $session->duration);
    }

    public function test_calculates_duration_with_finished_at()
    {
        $session = CheckSession::factory()->create([
            'created_at' => now()->subSeconds(200),
            'finished_at' => now()->subSeconds(50),
        ]);

        $this->assertEquals(150, $session->duration);
    }
}
