<?php

declare(strict_types=1);

use function Pest\Laravel\actingAs;
use function Pest\Laravel\freezeSecond;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;

pest()
    ->extend(TestCase::class)
    ->beforeEach(function (): void {
        freezeSecond();
    })
    ->in('Feature', 'Integration', 'Unit');

function login(null|Authenticatable $user = null): Authenticatable
{
    $user ??= User::factory()->createOne();

    actingAs($user);

    return $user;
}
