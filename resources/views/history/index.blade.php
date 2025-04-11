@extends('layouts.app')

@section('title', 'История проверок')

@section('content')
    <div class="relative w-full rounded-t-lg lg:rounded-t-none lg:rounded-tl-lg lg:rounded-r-lg lg:rounded-br-none bg-[#fff2f2] dark:bg-[#1D0002] overflow-hidden p-6">
        <div class="transition-all delay-300 translate-y-0 opacity-100 duration-750 starting:opacity-0 starting:translate-y-4">
            <h1 class="text-2xl font-medium mb-4 dark:text-[#EDEDEC]">История проверок</h1>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left dark:text-[#EDEDEC]">
                    <thead class="border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <tr>
                        <th class="p-2">Дата</th>
                        <th class="p-2">Всего прокси</th>
                        <th class="p-2">Рабочих</th>
                        <th class="p-2">Время</th>
                        <th class="p-2"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($checkSessions as $session)
                        <tr>
                            <td class="p-2">{{ $session->created_at->format('d.m.Y H:i') }}</td>
                            <td class="p-2">{{ $session->total_proxies }}</td>
                            <td class="p-2">{{ $session->working_proxies }}</td>
                            <td class="p-2">{{ $session->duration }} сек.</td>
                            <td class="p-2">
                                <a href="{{ route('history.show', $session->id) }}" class="text-[#F53003] hover:underline">
                                    Подробнее
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-2 text-center">История проверок пуста.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
