<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use HosmelQ\NameOfPerson\PersonName;
use HosmelQ\NameOfPerson\PersonNameCast;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Override;

/**
 * @property-read int $id
 * @property-read string $email
 * @property-read null|CarbonImmutable $email_verified_at
 * @property-read string $first_name
 * @property-read string $last_name
 * @property-read string $password
 * @property-read null|string $remember_token
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read PersonName $name
 */
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;

    #[Override]
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'name' => PersonNameCast::class,
            'password' => 'hashed',
        ];
    }
}
