<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CheckProxiesRequest;
use App\Services\CheckSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProxyCheckController extends Controller
{
    public function index(): View
    {
        return view('index');
    }

    public function check(CheckProxiesRequest $request, CheckSessionService $checkSessionService): RedirectResponse
    {
        $validatedProxies = $request->validated_proxies;
        $validProxies = $validatedProxies['valid'];
        $invalidProxies = $validatedProxies['invalid'];
        $totalProxies = $validatedProxies['total'];

        // Создаем сессию проверки
        $checkSession = $checkSessionService->createSession($totalProxies);

        // Запускаем проверку асинхронно
        $checkSessionService->startProxyCheck($validProxies, $checkSession->id);

        // Если есть невалидные прокси, добавляем предупреждение
        if ($invalidProxies) {
            $invalidList = implode(', ', $invalidProxies);
            session()->flash('warning', "Следующие строки были пропущены, так как не соответствуют формату ip:port: $invalidList");
        }

        return redirect()->route('history.show', $checkSession);
    }
}
