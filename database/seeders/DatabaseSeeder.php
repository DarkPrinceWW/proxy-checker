<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CheckSession;
use App\Models\ProxyCheck;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $proxies = [
            '1.2.3.4:8080',
            '5.6.7.8:3128',
        ];

        $checkSession = CheckSession::create([
            'total_proxies' => count($proxies),
            'working_proxies' => 0,
            'duration' => 10,
        ]);

        foreach ($proxies as $proxy) {
            [$ip, $port] = explode(':', $proxy);
            ProxyCheck::create([
                'check_session_id' => $checkSession->id,
                'ip' => $ip,
                'port' => $port,
                'status' => false,
                'error_count' => 3,
            ]);
        }
    }
}
