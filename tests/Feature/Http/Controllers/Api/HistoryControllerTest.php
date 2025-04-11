<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CheckSession;
use App\Models\Proxy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_check_session_proxies(): void
    {
        $session = CheckSession::factory()->create();
        Proxy::factory(2)->create(['check_session_id' => $session->id]);

        $response = $this->getJson(route('api.history.proxies', $session));

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => ['ip', 'port', 'status', 'type'],
            ],
        ]);
    }

    public function test_returns_check_session_status(): void
    {
        $session = CheckSession::factory()->create(['finished_at' => null]);
        Proxy::factory(2)->create(['check_session_id' => $session->id, 'status' => 'valid']);
        Proxy::factory(3)->create(['check_session_id' => $session->id, 'status' => 'pending']);

        $response = $this->getJson(route('api.history.status', $session));

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'progress' => 40.0, // 2 из 5
                'checked' => 2,
                'working' => 2,
                'total' => 5,
                'duration' => $session->duration,
                'completed' => false,
            ],
        ]);
    }
}
