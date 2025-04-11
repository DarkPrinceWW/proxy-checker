<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\ProxyValidatorContract;
use Tests\TestCase;

class ProxyValidatorTest extends TestCase
{
    private ProxyValidatorContract $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = app(ProxyValidatorContract::class);
    }

    public function test_validates_proxy_list(): void
    {
        $input = [
            '192.168.1.1:8080',
            'invalid',
            '256.256.256.256:8080',
            '172.16.0.1:3128',
        ];

        $result = $this->validator->validate($input);

        $this->assertEquals(['192.168.1.1:8080', '172.16.0.1:3128'], $result->valid->toArray());
        $this->assertEquals(['invalid', '256.256.256.256:8080'], $result->invalid->toArray());
        $this->assertEquals([
            'valid' => ['192.168.1.1:8080', '172.16.0.1:3128'],
            'invalid' => ['invalid', '256.256.256.256:8080'],
        ], $result->toArray());
    }

    public function test_handles_empty_input(): void
    {
        $result = $this->validator->validate([]);

        $this->assertEmpty($result->valid->toArray());
        $this->assertEmpty($result->invalid->toArray());
        $this->assertEquals([
            'valid' => [],
            'invalid' => [],
        ], $result->toArray());
    }

    public function test_trims_whitespace(): void
    {
        $input = [
            '  192.168.1.1:8080  ',
            '  172.16.0.1:3128  ',
        ];

        $result = $this->validator->validate($input);

        $this->assertEquals(['192.168.1.1:8080', '172.16.0.1:3128'], $result->valid->toArray());
        $this->assertEmpty($result->invalid->toArray());
        $this->assertEquals([
            'valid' => ['192.168.1.1:8080', '172.16.0.1:3128'],
            'invalid' => [],
        ], $result->toArray());
    }

    public function test_filters_empty_strings(): void
    {
        $input = [
            '192.168.1.1:8080',
            '',
            '  ',
            '172.16.0.1:3128',
        ];

        $result = $this->validator->validate($input);

        $this->assertEquals(['192.168.1.1:8080', '172.16.0.1:3128'], $result->valid->toArray());
        $this->assertEmpty($result->invalid->toArray());
        $this->assertEquals([
            'valid' => ['192.168.1.1:8080', '172.16.0.1:3128'],
            'invalid' => [],
        ], $result->toArray());
    }
}
