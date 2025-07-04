<?php

declare(strict_types=1);

arch()->preset()->php();
arch()->preset()->laravel();
arch()->preset()->security()->ignoring('assert');

arch('annotations')
    ->expect('App')
    ->toHaveMethodsDocumented()
    ->toHavePropertiesDocumented();

arch('strict types')
    ->expect('App')
    ->toUseStrictTypes();
