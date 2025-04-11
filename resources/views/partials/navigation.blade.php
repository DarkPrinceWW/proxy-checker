<nav class="w-full flex justify-between mb-6">
    <div class="flex gap-4">
        <a href="{{ route('proxies.index') }}" class="text-[#F53003] hover:underline {{ request()->routeIs('proxies.index') ? 'underline' : '' }}">
            Проверка прокси
        </a>
        <a href="{{ route('history') }}" class="text-[#F53003] hover:underline {{ request()->routeIs('history*') ? 'underline' : '' }}">
            История
        </a>
    </div>
</nav>
