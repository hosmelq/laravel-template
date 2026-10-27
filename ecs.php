<?php

declare(strict_types=1);

use HosmelQ\PhpCodingStandard\CodingStandard;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withCache(__DIR__.'/.cache/ecs')
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/bootstrap',
        __DIR__.'/config',
        __DIR__.'/config-validation',
        __DIR__.'/database',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->withRootFiles()
    ->withSets([CodingStandard::ECS_LARAVEL_APP]);
