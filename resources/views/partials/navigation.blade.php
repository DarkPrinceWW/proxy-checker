<nav class="w-full flex justify-between mb-6">
    <div class="flex gap-4">
        <a href="{{ route('home') }}" class="text-[#F53003] hover:underline {{ request()->routeIs('home') ? 'underline' : '' }}">
            Проверка прокси
        </a>
        <a href="{{ route('history') }}" class="text-[#F53003] hover:underline {{ request()->routeIs('history*') ? 'underline' : '' }}">
            История
        </a>
    </div>
</nav>
