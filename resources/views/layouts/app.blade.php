<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Прокси-чекер' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
<div class="w-full lg:max-w-4xl max-w-[335px] flex flex-col items-center">
    <!-- Navigation -->
    @include('partials.navigation')

    <!-- Main Content -->
    <main class="relative w-full flex flex-col lg:flex-row">
        @yield('content')
    </main>
</div>
<div class="h-14.5 hidden lg:block"></div>

<!-- Вывод стека scripts -->
@stack('scripts')
</body>
</html>
