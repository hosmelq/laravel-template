<?php

declare(strict_types=1);

use function Pest\Laravel\get;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

it('redirects guests to the admin login', function (): void {
    $response = get('/admin');

    $response->assertRedirect('/admin/login');
});

it('forbids non-admin users', function (): void {
    Config::set('admin.emails', ['owner@example.com']);

    login(User::factory()->createOne(['email' => 'user@example.com']));

    $response = get('/admin');

    $response->assertForbidden();
});

it('allows admins to open the panel', function (): void {
    Config::set('admin.emails', ['admin@example.com']);

    login(User::factory()->createOne(['email' => 'admin@example.com']));

    $response = get('/admin');

    $response->assertOk();
});

it('grants the admin ability only to admins', function (): void {
    Config::set('admin.emails', ['admin@example.com']);

    $admin = User::factory()->makeOne(['email' => 'admin@example.com']);
    $user = User::factory()->makeOne(['email' => 'user@example.com']);

    expect(Gate::forUser($admin)->allows('admin'))->toBeTrue()
        ->and(Gate::forUser($user)->allows('admin'))->toBeFalse();
});
