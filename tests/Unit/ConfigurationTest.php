<?php

declare(strict_types=1);

use Illuminate\Support\Env;
use Illuminate\Support\Facades\Config;
use Sentry\ClientBuilder;

it('publishes every agent configuration option', function (): void {
    $published = require base_path('vendor/laravel/boost/config/boost.php');
    $configured = require config_path('boost.php');

    expect($configured)->toHaveKeys(array_keys($published));
});

it('passes the configured sampling rates to the telemetry client', function (): void {
    $options = resolve(ClientBuilder::class)->getOptions();

    expect($options->getSampleRate())->toBe(1.0)
        ->and($options->getTracesSampleRate())->toBe(0.5)
        ->and($options->getProfilesSampleRate())->toBe(0.5)
        ->and(Config::boolean('sentry.tracing.redis_commands'))->toBeFalse();
});

it('allows environment overrides for agent controls and browser log levels', function (): void {
    $repository = Env::getRepository();
    $overrides = [
        'BOOST_ENABLED' => 'false',
        'BOOST_RULES_ENABLED' => 'false',
        'BOOST_RULES_SCOPED_GUIDELINES' => 'true',
        'BOOST_BROWSER_LOGS_WATCHER' => 'false',
        'BOOST_BROWSER_LOG_LEVELS' => 'error,warning',
        'BOOST_PHP_EXECUTABLE_PATH' => '/usr/local/bin/php',
    ];
    $original = [];

    foreach ($overrides as $key => $value) {
        $original[$key] = $repository->get($key);
        $repository->set($key, $value);
    }

    try {
        $configuration = require config_path('boost.php');

        expect($configuration['enabled'])->toBeFalse()
            ->and($configuration['rules'])->toBe(['enabled' => false, 'scoped_guidelines' => true])
            ->and($configuration['browser_logs_watcher'])->toBeFalse()
            ->and($configuration['browser_log_levels'])->toBe(['error', 'warning'])
            ->and($configuration['executable_paths']['php'])->toBe('/usr/local/bin/php')
            ->and($configuration['enforce_tests'])->toBeTrue();
    } finally {
        foreach ($original as $key => $value) {
            if ($value === null) {
                $repository->clear($key);
            } else {
                $repository->set($key, $value);
            }
        }
    }
});
