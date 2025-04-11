<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CheckSession;
use App\Services\CheckSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CheckSessionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_status_returns_json_response(): void
    {
        $session = CheckSession::factory()->create(['total_proxies' => 5]);
        $checkSessionService = Mockery::mock(CheckSessionService::class);

        $status = [
            'progress' => 40.0,
            'checked' => 2,
            'total' => 5,
            'duration' => 10,
            'completed' => false,
        ];

        $checkSessionService->shouldReceive('getCheckStatus')
            ->once()
            ->with(Mockery::on(function($arg) use ($session) {
                return $arg instanceof CheckSession && $arg->id === $session->id;
            }))
            ->andReturn($status);

        $this->app->instance(CheckSessionService::class, $checkSessionService);

        $response = $this->get(route('check.status', $session));

        $response->assertStatus(200);
        $response->assertJson($status);
    }
}
