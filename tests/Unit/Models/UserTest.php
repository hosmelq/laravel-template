<?php

declare(strict_types=1);

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Hash;

it('correctly casts attributes', function (): void {
    $user = new User([
        'created_at' => '2025-02-02 01:00:36',
        'email_verified_at' => '2025-02-02 01:00:36',
        'password' => bcrypt('password'),
        'updated_at' => '2025-02-02 01:00:36',
    ]);

    expect($user)
        ->created_at->toBeInstanceOf(CarbonImmutable::class)
        ->email_verified_at->toBeInstanceOf(CarbonImmutable::class)
        ->updated_at->toBeInstanceOf(CarbonImmutable::class)
        ->and(Hash::needsRehash($user->password))->toBeFalse();
});
