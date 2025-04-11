<?php

declare(strict_types=1);

namespace App\Rules;

use App\Contracts\ProxyValidatorContract;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidProxyListRule implements ValidationRule
{
    protected ?Closure $onSuccess = null;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value)) {
            $value = explode("\n", $value);
        }

        $result = app(ProxyValidatorContract::class)->validate($value);

        if ($result->invalid->isNotEmpty()) {
            $fail('Прокси должны быть в формате ip:port.');

            return;
        }

        if ($this->onSuccess) {
            ($this->onSuccess)($result->valid);
        }
    }

    public function onSuccess(Closure $callback): self
    {
        $this->onSuccess = $callback;

        return $this;
    }
}
