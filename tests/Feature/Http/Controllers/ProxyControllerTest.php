<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Jobs\CheckProxyJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProxyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_displays_home_page(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    public function test_creates_check_session_and_dispatches_jobs(): void
    {
        Queue::fake();

        $response = $this->post(route('check'), [
            'proxies' => "192.168.1.1:8080\n172.16.0.1:3128",
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('check_sessions', ['finished_at' => null]);
        $this->assertDatabaseHas('proxies', ['ip' => '192.168.1.1', 'port' => 8080]);
        $this->assertDatabaseHas('proxies', ['ip' => '172.16.0.1', 'port' => 3128]);

        Queue::assertPushed(CheckProxyJob::class, 2);
    }

    public function test_fails_with_invalid_proxies(): void
    {
        $response = $this->post(route('check'), [
            'proxies' => "invalid\n256.256.256.256:8080",
        ]);

        $response->assertSessionHasErrors('proxies');
    }
}
