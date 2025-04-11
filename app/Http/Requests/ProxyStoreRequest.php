<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\ValidProxyListRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class ProxyStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proxies' => [
                'required',
                'string',
                (new ValidProxyListRule)->onSuccess(
                    fn(Collection $validProxies) => $this->merge(['proxies' => $validProxies])
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'proxies.required' => 'Поле прокси обязательно для заполнения.',
        ];
    }
}
