<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;
use Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase {
        migrateFreshUsing as baseMigrateFreshUsing;
    }

    /**
     * {@inheritDoc}
     */
    protected function migrateFreshUsing(): array
    {
        return array_merge($this->baseMigrateFreshUsing(), [
            '--path' => [
                'database/migrations',
                'tests/migrations',
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();

        config([
            'media-library.file_namer' => DefaultFileNamer::class,
            'media-library.path_generator' => DefaultPathGenerator::class,
        ]);
    }
}
