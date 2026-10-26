<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return new Configuration()
    ->addPathToScan(__DIR__.'/config-validation', isDev: false)
    ->addPathToScan(__DIR__.'/database/migrations', isDev: false)
    ->disableReportingUnmatchedIgnores()
    ->ignoreErrors([ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnExtensions([
        'ext-bcmath',
    ], [ErrorType::UNUSED_DEPENDENCY])
    ->ignoreErrorsOnPackages([
        'archtechx/enums',
        'laravel/octane',
        'laravel/tinker',
        'propaganistas/laravel-phone',
        'resend/resend-php',
        'thecodingmachine/safe',
        'tpetry/laravel-query-expressions',
    ], [ErrorType::UNUSED_DEPENDENCY]);
