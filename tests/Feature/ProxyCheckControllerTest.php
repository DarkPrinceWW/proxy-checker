<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CheckSession;
use App\Services\CheckSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ProxyCheckControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_view(): void
    {
        $response = $this->get(route('proxies.index'));

        $response->assertStatus(200);
        $response->assertViewIs('index');
    }

    public function test_check_creates_session_and_redirects(): void
    {
        $checkSessionService = Mockery::mock(CheckSessionService::class);
        $checkSession = CheckSession::factory()->create(['total_proxies' => 2]);

        $proxies = "1.2.3.4:8080\n5.6.7.8:3128";
        $validatedProxies = [
            'valid' => ['1.2.3.4:8080', '5.6.7.8:3128'],
            'invalid' => [],
            'total' => 2,
        ];

        $checkSessionService->shouldReceive('createSession')
            ->once()
            ->with(2)
            ->andReturn($checkSession);

        $checkSessionService->shouldReceive('startProxyCheck')
            ->once()
            ->with($validatedProxies['valid'], $checkSession->id);

        $this->app->instance(CheckSessionService::class, $checkSessionService);

        $response = $this->post(route('proxies.check'), [
            'proxies' => $proxies,
        ], ['X-CSRF-TOKEN' => csrf_token()]);

        $response->assertStatus(302);
        $response->assertRedirect(route('history.show', $checkSession));
        $this->assertNull(session('warning'));
    }
}
