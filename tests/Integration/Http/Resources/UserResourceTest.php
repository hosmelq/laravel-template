<?php

declare(strict_types=1);

use App\Models\User;

it('formats resource correctly', function (): void {
    $user = User::factory()->createOne([
        'email' => ' TEST@example.com ',
    ]);

    $resource = json_decode($user->toResource()->toJson(), true);

    expect([
        'avatar_url' => sprintf(
            'https://www.gravatar.com/avatar/%s?d=mp',
            '973dfe463ec85785f5f95af5ba3906eedb2d931c24e69824a89ea65dba4e813b'
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
