<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Inertia\Middleware;
use Override;

class HandleInertiaRequests extends Middleware
{
    #[Override]
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => $this->authProperties($request),
        ];
    }

    /**
     * @return array{user: null|JsonResource}
     */
    private function authProperties(Request $request): array
    {
        $user = $request->user();

        return [
            'user' => $user?->toResource(),
        ];
    }
}
