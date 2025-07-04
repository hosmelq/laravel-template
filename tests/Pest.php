<?php

declare(strict_types=1);

use function Pest\Laravel\actingAs;
use function Pest\Laravel\freezeSecond;
use function Pest\Laravel\withoutVite;

use App\Enums\ToastVariant;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

pest()
    ->extend(TestCase::class)
    ->beforeEach(function (): void {
        freezeSecond();
        withoutVite();
    })
    ->in('Feature', 'Integration', 'Unit');

function login(null|Authenticatable $user = null): User
{
    $user ??= User::factory()->createOne();

    actingAs($user);

    return $user;
}

TestResponse::macro('assertToast', function (
    string $title,
    null|string $description = null,
    ToastVariant $variant = ToastVariant::Success,
    int $timeout = 5
): TestResponse {
    return $this->assertSessionHas('inertia.flash_data.toast', array_filter([
        'description' => $description,
        'timeout' => $timeout * 1000,
        'title' => $title,
        'variant' => $variant->value,
    ], static fn (null|int|string $value): bool => $value !== null));
});
