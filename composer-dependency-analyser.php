<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return new Configuration()
    ->ignoreErrors([ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnPackages([
        'archtechx/enums',
        'laravel/tinker',
        'propaganistas/laravel-disposable-email',
        'thecodingmachine/safe',
    ], [ErrorType::UNUSED_DEPENDENCY]);
