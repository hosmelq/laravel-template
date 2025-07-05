<?php

declare(strict_types=1);

use App\Providers\TestingServiceProvider;

arch()->preset()->php();
arch()->preset()->laravel()->ignoring(TestingServiceProvider::class);
arch()->preset()->security()->ignoring('assert');

arch('annotations')
    ->expect('App')
    ->toHaveMethodsDocumented()
    ->toHavePropertiesDocumented();

arch('strict types')
    ->expect('App')
    ->toUseStrictTypes();
