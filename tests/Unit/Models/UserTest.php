<?php

declare(strict_types=1);

use App\Models\User;
use Carbon\CarbonImmutable;
use HosmelQ\NameOfPerson\PersonName;
use Illuminate\Support\Facades\Hash;

it('correctly casts attributes', function (): void {
    $user = new User([
        'created_at' => '2025-02-02 01:00:36',
        'email_verified_at' => '2025-02-02 01:00:36',
        'first_name' => 'Hosmel',
        'last_name' => 'Quintana',
        'password' => bcrypt('password'),
        'updated_at' => '2025-02-02 01:00:36',
    ]);

    expect($user)
        ->created_at->toBeInstanceOf(CarbonImmutable::class)
        ->email_verified_at->toBeInstanceOf(CarbonImmutable::class)
        ->name->toBeInstanceOf(PersonName::class)
        ->name->full()->toBe('Hosmel Quintana')
        ->updated_at->toBeInstanceOf(CarbonImmutable::class)
        ->and(Hash::needsRehash($user->password))->toBeFalse();
});
