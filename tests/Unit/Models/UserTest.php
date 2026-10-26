<?php

declare(strict_types=1);

use App\Models\User;
use Carbon\CarbonImmutable;
use Filament\Facades\Filament;
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

it('determines admin status based on config allowlist', function (): void {
    config(['admin.emails' => ['owner@example.com']]);

    $admin = new User(['email' => 'owner@example.com']);
    $user = new User(['email' => 'user@example.com']);

    expect($admin->isAdmin())->toBeTrue()
        ->and($user->isAdmin())->toBeFalse();
});

it('allows only admins to access the admin panel', function (): void {
    config(['admin.emails' => ['owner@example.com']]);

    $panel = Filament::getPanel('admin');

    expect(new User(['email' => 'owner@example.com'])->canAccessPanel($panel))->toBeTrue()
        ->and(new User(['email' => 'user@example.com'])->canAccessPanel($panel))->toBeFalse();
});

it('normalizes email addresses when assigned', function (): void {
    $user = new User();

    $user->email = 'Mariela@Example.com';

    expect($user->email)->toBe('mariela@example.com');
});

it('identifies itself in audit logs', function (): void {
    $user = User::factory()->makeOne(['email' => 'owner@example.com', 'id' => 7]);

    $identity = $user->auditLogIdentity();

    expect($identity)
        ->id->toBe('7')
        ->name->toBe('owner@example.com')
        ->type->toBe('user');
});
