<?php

declare(strict_types=1);

use HosmelQ\PhpCodingStandard\CodingStandard;
use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withCache('./.cache/rector', FileCacheStorage::class)
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
    ->withSets([CodingStandard::RECTOR_LARAVEL_APP, CodingStandard::RECTOR_PEST]);
