<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', fn (): array => [
    'laravel' => app()->version(),
])->name('home');
