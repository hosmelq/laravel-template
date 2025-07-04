<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, array<mixed>|Rule|string>
     */
    protected function passwordRules(): array // @phpstan-ignore-line return.deprecatedInterface
    {
        return ['required', 'string', Password::default(), 'confirmed'];
    }
}
