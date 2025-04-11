<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\ProxyChecker\DTOs\ProxyCheckerResult;
use App\Services\ProxyChecker\Providers\IpApiProxyChecker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IpApiProxyCheckerTest extends TestCase
{
    public function test_check_successful_response(): void
    {
        Http::fake([
            'http://ip-api.com/json' => Http::response([
                'status' => 'success',
                'country' => 'USA',
                'city' => 'New York',
                'query' => '8.8.8.8',
            ], 200),
        ]);

        $checker = new IpApiProxyChecker(['timeout' => 5]);
        $result = $checker->check('1.2.3.4', '8080'); // Убрали третий параметр 'HTTP'

        $this->assertInstanceOf(ProxyCheckerResult::class, $result);
        $this->assertTrue($result->status);
        $this->assertEquals('SOCKS5', $result->type); // Ожидаем SOCKS5, так как это первый тип в переборе
        $this->assertEquals('USA', $result->country);
        $this->assertEquals('New York', $result->city);
        $this->assertEquals('8.8.8.8', $result->externalIp);
        $this->assertGreaterThan(0, $result->responseTime);
    }

    public function test_check_failed_response(): void
    {
        Http::fake([
            'http://ip-api.com/json' => Http::response(null, 500),
        ]);

        $checker = new IpApiProxyChecker(['timeout' => 5]);
        $result = $checker->check('1.2.3.4', '8080'); // Убрали третий параметр 'HTTP'

        $this->assertFalse($result);
    }
}
