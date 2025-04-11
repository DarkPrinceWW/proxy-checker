<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\ProxyCheckerContract;
use App\Contracts\ProxyValidatorContract;
use App\Services\ProxyChecker\IpApiProxyChecker;
use App\Services\ProxyValidator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProxyValidatorContract::class, ProxyValidator::class);
        $this->app->bind(ProxyCheckerContract::class, IpApiProxyChecker::class);
    }
}
