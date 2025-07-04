<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * {@inheritDoc}
     *
     * @param array<string, string> $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'email' => [
                'required',
                'string',
                'max:255',
                'email:strict,dns',
                'indisposable',
                Rule::unique(User::class),
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::query()->create([
            'email' => $input['email'],
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
