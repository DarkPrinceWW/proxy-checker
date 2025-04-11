<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Resources\CheckSessionResource;
use App\Models\CheckSession;
use App\Models\ProxyCheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_view_with_sessions(): void
    {
        $sessions = CheckSession::factory()->count(3)->create();

        $response = $this->get(route('history'));

        $response->assertStatus(200);
        $response->assertViewIs('history');
        $response->assertViewHas('checkSessions', function($collection) {
            return $collection->count() === 3;
        });
    }

    public function test_show_returns_view_with_session_and_checks(): void
    {
        $session = CheckSession::factory()->create();
        $checks = ProxyCheck::factory()->count(2)->create(['check_session_id' => $session->id]);

        $response = $this->get(route('history.show', $session));

        $response->assertStatus(200);
        $response->assertViewIs('show');
        $response->assertViewHas('checkSession', function($resource) use ($session) {
            $expected = CheckSessionResource::make($session)->toArray(null);
            $actual = $resource->toArray(null);

            return $expected['id'] === $actual['id'] &&
                $expected['total_proxies'] === $actual['total_proxies'];
        });
        $response->assertViewHas('proxyChecks', function($collection) {
            return $collection->count() === 2;
        });
    }

    public function test_get_results_returns_json_collection(): void
    {
        $session = CheckSession::factory()->create();
        $checks = ProxyCheck::factory()->count(2)->create(['check_session_id' => $session->id]);

        $response = $this->get(route('history.results', $session));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'check_session_id',
                    'ip',
                    'port',
                    'status',
                    'type',
                    'country',
                    'city',
                    'response_time',
                    'external_ip',
                    'error_count',
                ],
            ],
        ]);
        $response->assertJsonCount(2, 'data');
    }
}
