<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ProxyChecker;

use App\Enums\ProxyTypeEnum;
use App\Services\ProxyChecker\IpApiProxyChecker;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IpApiProxyCheckerTest extends TestCase
{
    public function test_checks_proxy_successfully(): void
    {
        Http::fake();

        $response = $this->createMock(Response::class);
        $response->method('successful')->willReturn(true);
        $response->method('json')->willReturn([
            'country' => 'USA',
            'city' => 'New York',
            'query' => '172.16.0.1',
        ]);
        $response->method('handlerStats')->willReturn(['total_time' => 0.2]);
        $response->method('status')->willReturn(200);
        $response->method('reason')->willReturn('OK');

        Http::shouldReceive('pool')->once()->andReturn([
            'http' => $response,
        ]);

        $result = (new IpApiProxyChecker)->check('192.168.1.1', 8080);

        $this->assertNotNull($result);
        $this->assertEquals(ProxyTypeEnum::Http, $result->type);
        $this->assertEquals('USA', $result->country);
        $this->assertEquals('New York', $result->city);
        $this->assertEquals('172.16.0.1', $result->externalIp);
        $this->assertEquals(200.0, $result->responseTime); // 0.2 * 1000
    }

    public function test_returns_null_on_failure(): void
    {
        Http::fake([
            'http://ip-api.com/json' => Http::response(null, 500),
        ]);

        $result = (new IpApiProxyChecker)->check('192.168.1.1', 8080);

        $this->assertNull($result);
    }
}
