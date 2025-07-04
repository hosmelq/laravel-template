<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Override;

/**
 * @property User $resource
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'avatar_url' => sprintf(
                'https://www.gravatar.com/avatar/%s?d=mp',
                Str::of($this->resource->email)->trim()->lower()->hash('sha256')
            ),
            'created_at' => $this->resource->created_at,
            'email' => $this->resource->email,
            'first_name' => $this->resource->first_name,
            'id' => $this->resource->id,
            'is_email_verified' => $this->resource->email_verified_at !== null,
            'last_name' => $this->resource->last_name,
            'name' => $this->resource->name,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
