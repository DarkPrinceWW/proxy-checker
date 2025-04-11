@extends('layouts.app')

@section('title', 'Проверка прокси')

@section('content')
    <div class="relative w-full rounded-t-lg lg:rounded-t-none lg:rounded-tl-lg lg:rounded-r-lg lg:rounded-br-none bg-[#fff2f2] dark:bg-[#1D0002] overflow-hidden p-6">
        <h1 class="text-2xl font-medium mb-4 dark:text-[#EDEDEC]">Проверка прокси</h1>
        <div class="transition-all delay-300 translate-y-0 opacity-100 duration-750 starting:opacity-0 starting:translate-y-4">
            <!-- Отображение предупреждений -->
            @if (session('warning'))
                <div class="mb-4 p-3 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded-sm text-sm">
                    {{ session('warning') }}
                </div>
            @endif

            <!-- Отображение ошибок валидации -->
            @if ($errors->has('proxies'))
                <div class="mb-4 p-3 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-sm text-sm">
                    {{ $errors->first('proxies') }}
                </div>
            @endif

            <form action="{{ route('proxies.check') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="proxies" class="block text-sm font-medium dark:text-[#EDEDEC] mb-2">
                        Введите список прокси (формат: ip:port, по одному на строку)
                    </label>
                    <textarea
                        id="proxies"
                        name="proxies"
                        rows="10"
                        class="w-full p-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm text-sm dark:text-[#EDEDEC] dark:bg-[#0a0a0a]"
                        placeholder="1.2.3.4:8080&#10;5.6.7.8:3128"
                    >{{ old('proxies') }}</textarea>
                </div>
                <button
                    type="submit"
                    class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                >
                    Проверить
                </button>
            </form>
        </div>
    </div>
@endsection
