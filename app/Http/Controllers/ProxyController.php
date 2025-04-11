<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ProxyStatusEnum;
use App\Http\Requests\ProxyStoreRequest;
use App\Jobs\CheckProxyJob;
use App\Models\CheckSession;
use App\Models\Proxy;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProxyController extends Controller
{
    public function index(): View
    {
        return view('home');
    }

    public function store(ProxyStoreRequest $request): RedirectResponse
    {
        $checkSession = CheckSession::create();

        foreach ($request->get('proxies') as $proxy) {
            [$ip, $port] = explode(':', $proxy);

            /** @var Proxy $proxy */
            $proxy = $checkSession->proxies()->create([
                'status' => ProxyStatusEnum::Pending,
                'type' => null,
                'ip' => $ip,
                'port' => $port,
            ]);

            CheckProxyJob::dispatch($proxy);
        }

        return redirect()->route('history.show', $checkSession);
    }
}
