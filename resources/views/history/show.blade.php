@extends('layouts.app')

@section('title', 'Детали проверки')

@section('content')
    <div
        class="relative w-full rounded-t-lg lg:rounded-t-none lg:rounded-tl-lg lg:rounded-r-lg lg:rounded-br-none bg-[#fff2f2] dark:bg-[#1D0002] overflow-hidden p-6">
        <div
            class="transition-all delay-300 translate-y-0 opacity-100 duration-750 starting:opacity-0 starting:translate-y-4">
            <h1 class="text-2xl font-medium mb-4 dark:text-[#EDEDEC]">
                Проверка от {{ $checkSession->created_at->format('d.m.Y H:i') }}
            </h1>

            <!-- Отображение предупреждений -->
            @if (session('warning'))
                <div
                    class="mb-4 p-3 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded-sm text-sm">
                    {{ session('warning') }}
                </div>
            @endif

            <!-- Спиннер загрузки -->
            <div id="loading-spinner" class="flex justify-center mb-4">
                <svg class="animate-spin h-6 w-6 text-[#F53003]" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Текстовый индикатор -->
            <p id="status-text" class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2">
                Инициализация проверки...
            </p>

            <!-- Прогресс-бар -->
            <div class="mb-4">
                <div id="progress-container" class="w-full bg-gray-200 rounded-full h-4 dark:bg-[#3E3E3A]">
                    <div id="progress-bar" class="bg-[#F53003] h-4 rounded-full progress-bar-waiting"
                         style="width: 0%"></div>
                </div>
                <p id="progress-text" class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2" />
                <p id="duration-text" class="text-sm text-[#706f6c] dark:text-[#A1A09A]" />
                <p id="working-text" class="text-sm text-[#706f6c] dark:text-[#A1A09A]" />
            </div>

            <!-- Таблица результатов -->
            <div class="overflow-x-auto">
                <table id="results-table" class="w-full text-sm text-left dark:text-[#EDEDEC]">
                    <thead class="border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                        <tr>
                            <th class="p-2">Прокси</th>
                            <th class="p-2">Тип</th>
                            <th class="p-2">Страна/Город</th>
                            <th class="p-2">Статус</th>
                            <th class="p-2">Время ответа</th>
                            <th class="p-2">Внешний IP</th>
                        </tr>
                    </thead>
                    <tbody id="results-body">
                        @if ($proxies->isEmpty())
                            <tr>
                                <td colspan="6" class="p-2 text-center">Ожидание результатов...</td>
                            </tr>
                        @else
                            @foreach ($proxies as $check)
                                <tr>
                                    <td class="p-2">{{ $check->ip }}:{{ $check->port }}</td>
                                    <td class="p-2">{{ $check->type ?? '-' }}</td>
                                    <td class="p-2">{{ $check->country ?? '-' }} / {{ $check->city ?? '-' }}</td>
                                    <td class="p-2">
                                        @switch($check->status)
                                            @case(\App\Enums\ProxyStatusEnum::Pending->value)
                                                Ожидает
                                                @break
                                            @case(\App\Enums\ProxyStatusEnum::Valid->value)
                                                Работает
                                                @break
                                            @case(\App\Enums\ProxyStatusEnum::Invalid->value)
                                                Не работает
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="p-2">{{ $check->response_time ? $check->response_time . ' мс' : '-' }}</td>
                                    <td class="p-2">{{ $check->external_ip ?? '-' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <a
                href="{{ route('history') }}"
                class="inline-block mt-4 px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
            >
                Назад к истории
            </a>
        </div>
    </div>

    <style>
        .progress-bar-waiting {
            background: linear-gradient(
                45deg,
                #F53003 25%,
                #E53C12 25%,
                #E53C12 50%,
                #F53003 50%,
                #F53003 75%,
                #E53C12 75%,
                #E53C12
            );
            background-size: 40px 40px;
            animation: move-stripes 1s linear infinite;
        }

        @keyframes move-stripes {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: 40px 0;
            }
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const totalProxies = {{ $checkSession->total_proxies }};
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const durationText = document.getElementById('duration-text');
            const workingText = document.getElementById('working-text');
            const resultsBody = document.getElementById('results-body');
            const loadingSpinner = document.getElementById('loading-spinner');
            const statusText = document.getElementById('status-text');

            const MAX_WAIT_TIME = 300000; // 5 минут в миллисекундах
            let startTime = Date.now();

            function updateProgress() {
                if (Date.now() - startTime > MAX_WAIT_TIME) {
                    statusText.textContent = 'Превышено время ожидания. Проверка остановлена.';
                    progressBar.classList.remove('progress-bar-waiting');
                    if (loadingSpinner) {
                        loadingSpinner.classList.add('hidden');
                    }
                    return;
                }

                fetch("{{ route('api.history.status', ['checkSession' => $checkSession->id]) }}")
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(results => {
                        const data = results.data || [];
                        const progress = data.progress;
                        const checked = data.checked;
                        const working = data.working;
                        const duration = data.duration;
                        const completed = data.completed;

                        progressBar.style.width = `${progress}%`;
                        progressText.textContent = `Проверено: ${checked} из ${totalProxies} прокси`;
                        durationText.textContent = `Время проверки: ${duration} сек.`;
                        workingText.textContent = `Рабочих прокси: ${working}`;

                        if (checked === 0) {
                            statusText.textContent = 'Проверка началась...';
                        } else if (!completed) {
                            statusText.textContent = 'Проверка выполняется...';
                        } else {
                            statusText.textContent = 'Проверка завершена';
                        }

                        if (checked > 0) {
                            progressBar.classList.remove('progress-bar-waiting');
                            if (loadingSpinner) {
                                loadingSpinner.classList.add('hidden');
                            }
                        }

                        fetch("{{ route('api.history.proxies', ['checkSession' => $checkSession->id]) }}")
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok: ' + response.statusText);
                                }
                                return response.json();
                            })
                            .then(results => {
                                resultsBody.innerHTML = '';
                                const data = results.data || [];
                                if (data.length === 0) {
                                    resultsBody.innerHTML = '<tr><td colspan="6" class="p-2 text-center">Нет результатов</td></tr>';
                                } else {
                                    data.forEach(check => {
                                        const row = document.createElement('tr');
                                        row.innerHTML = `
                                            <td class="p-2">${check.ip}:${check.port}</td>
                                            <td class="p-2">${check.type || '-'}</td>
                                            <td class="p-2">${check.country || '-'} / ${check.city || '-'}</td>
                                            <td class="p-2">
                                                ${check.status === 'pending' ? 'Ожидает' : check.status === 'valid' ? 'Работает' : 'Не работает'}
                                            </td>
                                            <td class="p-2">${check.response_time ? check.response_time + ' мс' : '-'}</td>
                                            <td class="p-2">${check.external_ip || '-'}</td>
                                        `;
                                        resultsBody.appendChild(row);
                                    });
                                }
                            })
                            .catch(() => {
                                resultsBody.innerHTML = '<tr><td colspan="6" class="p-2 text-center">Ошибка при загрузке результатов</td></tr>';
                            });

                        if (completed) {
                            progressBar.classList.remove('progress-bar-waiting');
                            if (loadingSpinner) {
                                loadingSpinner.classList.add('hidden');
                            }
                        }

                        setTimeout(updateProgress, 1000);
                    })
                    .catch(error => {
                        statusText.textContent = 'Произошла ошибка при проверке';
                        progressBar.classList.remove('progress-bar-waiting');
                        if (loadingSpinner) {
                            loadingSpinner.classList.add('hidden');
                        }
                    });
            }

            updateProgress();
        });
    </script>
@endpush
