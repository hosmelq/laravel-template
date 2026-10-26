<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_if(
            $request->user() === null || ! $request->user()->isAdmin(),
            Response::HTTP_FORBIDDEN
        );

        return $next($request);
    }
}
