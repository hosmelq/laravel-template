<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Health\Http\Controllers\SimpleHealthCheckController;

Route::get('up', SimpleHealthCheckController::class);

Route::get('/', function (): Response {
    return Inertia::render('Home', [
        'now' => now()->toDateTimeString(),
    ]);
})->name('home');

Route::middleware('guest')->group(function (): void {
});

Route::middleware('auth')->group(function (): void {
    Route::middleware('verified')->scopeBindings()->group(function (): void {
    });
});
