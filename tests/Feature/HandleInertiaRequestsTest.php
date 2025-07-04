<?php

declare(strict_types=1);

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

it('shares a null auth user for guests', function (): void {
    $request = Request::create('/');
    $request->setUserResolver(fn (): null => null);

    $shared = resolve(HandleInertiaRequests::class)->share($request);

    expect($shared['auth']['user'])->toBeNull();
});

it('shares the authenticated user resource', function (): void {
    $user = User::factory()->createOne();
    $request = Request::create('/');
    $request->setUserResolver(fn (): User => $user);

    $shared = resolve(HandleInertiaRequests::class)->share($request);

    expect($shared['auth']['user'])
        ->toBeInstanceOf(UserResource::class)
        ->resource->toBe($user);
});
