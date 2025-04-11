<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\CheckSession;
use App\Models\Proxy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class HistoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_displays_history_index(): void
    {
        CheckSession::factory(3)->create();

        $response = $this->get(route('history'));

        $response->assertStatus(200);
        $response->assertViewIs('history.index');
        $response->assertViewHas('checkSessions', fn(Collection $collection) => $collection->count() === 3);
    }

    public function test_displays_history_show(): void
    {
        $session = CheckSession::factory()->create();
        Proxy::factory(2)->create(['check_session_id' => $session->id]);

        $response = $this->get(route('history.show', $session));

        $response->assertStatus(200);
        $response->assertViewIs('history.show');
        $response->assertViewHas('checkSession', $session);
        $response->assertViewHas('proxies', fn(Collection $proxies) => $proxies->count() === 2);
    }
}
