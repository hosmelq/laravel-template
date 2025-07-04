<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return new Configuration()
    ->addPathToScan(__DIR__.'/database/migrations', isDev: false)
    ->ignoreErrors([ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnPackages([
        'inertiaui/modal',
        'laravel/tinker',
        'laravel/wayfinder',
        'league/flysystem-aws-s3-v3',
        'propaganistas/laravel-disposable-email',
        'propaganistas/laravel-phone',
        'thecodingmachine/safe',
        'tpetry/laravel-query-expressions',
    ], [ErrorType::UNUSED_DEPENDENCY]);
