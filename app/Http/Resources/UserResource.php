<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'avatar_url' => sprintf(
                'https://www.gravatar.com/avatar/%s?d=mp',
                hash('sha256', mb_strtolower($this->email))
            ),
            'created_at' => $this->created_at,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'id' => $this->id,
            'is_email_verified' => ! is_null($this->email_verified_at),
            'last_name' => $this->last_name,
            'name' => $this->name,
            'updated_at' => $this->updated_at,
        ];
    }
}
