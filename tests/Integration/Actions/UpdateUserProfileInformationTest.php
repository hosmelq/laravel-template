<?php

declare(strict_types=1);

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

it('validates fields', function (array $data, array $expected): void {
    $user = User::factory()->createOne();

    expect(fn () => resolve(UpdateUserProfileInformation::class)->update($user, $data))
        ->toThrow(function (ValidationException $exception) use ($expected): void {
            expect($exception->validator->errors()->messages())->toBe($expected);
        });
})->with([
    'required' => [
        'data' => [
            'email' => '',
            'first_name' => '',
            'last_name' => '',
        ],
        'expected' => [
            'email' => ['The email field is required.'],
            'first_name' => ['The first name field is required.'],
            'last_name' => ['The last name field is required.'],
        ],
    ],
    'max:255 (string)' => [
        'data' => [
            'email' => 'valid@example.com',
            'first_name' => Str::repeat('a', 256),
            'last_name' => Str::repeat('a', 256),
        ],
        'expected' => [
            'first_name' => ['The first name field must not be greater than 255 characters.'],
            'last_name' => ['The last name field must not be greater than 255 characters.'],
        ],
    ],
]);

it('validates email uniqueness', function (): void {
    User::factory()->createOne(['email' => 'taken@example.com']);

    $user = User::factory()->createOne();

    expect(fn () => resolve(UpdateUserProfileInformation::class)->update($user, [
        'email' => 'taken@example.com',
        'first_name' => 'Jane',
        'last_name' => 'Doe',
    ]))->toThrow(function (ValidationException $exception): void {
        expect($exception->validator->errors()->messages()['email'])
            ->toBe(['The email has already been taken.']);
    });
});

it('updates the user profile name fields', function (): void {
    $user = User::factory()->createOne([
        'email' => 'old@example.com',
        'email_verified_at' => now(),
        'first_name' => 'Old',
        'last_name' => 'Name',
    ]);

    resolve(UpdateUserProfileInformation::class)->update($user, [
        'email' => 'old@example.com',
        'first_name' => 'New',
        'last_name' => 'Name',
    ]);

    expect($user->refresh())
        ->email->toBe('old@example.com')
        ->email_verified_at->not->toBeNull()
        ->first_name->toBe('New')
        ->last_name->toBe('Name');
});

it('marks the email as unverified when the email changes', function (): void {
    Notification::fake();

    $user = User::factory()->createOne([
        'email' => 'old@example.com',
        'email_verified_at' => now(),
        'first_name' => 'Old',
        'last_name' => 'Name',
    ]);

    resolve(UpdateUserProfileInformation::class)->update($user, [
        'email' => 'new@example.com',
        'first_name' => 'New',
        'last_name' => 'Name',
    ]);

    expect($user->refresh())
        ->email->toBe('new@example.com')
        ->email_verified_at->toBeNull()
        ->first_name->toBe('New')
        ->last_name->toBe('Name');

    Notification::assertSentTo($user, VerifyEmail::class);
});
