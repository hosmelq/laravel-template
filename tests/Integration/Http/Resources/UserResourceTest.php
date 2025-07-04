<?php

declare(strict_types=1);

use App\Http\Resources\UserResource;
use App\Models\User;

it('formats resource correctly', function (): void {
    $user = User::factory()->createOne();

    $resource = json_decode(UserResource::make($user)->toJson(), true);

    expect([
        'avatar_url' => sprintf(
            'https://www.gravatar.com/avatar/%s?d=mp',
            hash('sha256', mb_strtolower($user->email))
        ),
        'created_at' => $user->created_at->toJSON(),
        'email' => $user->email,
        'first_name' => $user->first_name,
        'id' => $user->id,
        'is_email_verified' => true,
        'last_name' => $user->last_name,
        'name' => $user->name,
        'updated_at' => $user->updated_at->toJSON(),
    ])->toMatchArray($resource);
});
