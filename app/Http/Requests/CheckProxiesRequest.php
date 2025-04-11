<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Contracts\ProxyValidatorInterface;
use Illuminate\Foundation\Http\FormRequest;

class CheckProxiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proxies' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'proxies.required' => 'Поле прокси обязательно для заполнения.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function($validator) {
            $proxiesInput = $this->get('proxies') ?? '';
            $result = app(ProxyValidatorInterface::class)->parseAndValidateProxies($proxiesInput);

            if ($result['total'] === 0) {
                $validator->errors()->add(
                    'proxies',
                    'Не найдено ни одного валидного прокси в формате ip:port.'
                );
            }

            // Сохраняем результат валидации в запросе для дальнейшего использования
            $this->merge([
                'validated_proxies' => $result,
            ]);
        });
    }
}
