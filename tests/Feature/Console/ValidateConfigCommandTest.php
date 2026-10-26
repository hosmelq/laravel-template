<?php

declare(strict_types=1);

use function Pest\Laravel\artisan;

it('rejects an application key that cannot initialize encryption', function (): void {
    app()->detectEnvironment(fn (): string => 'production');
    config([
        'app.debug' => false,
        'app.key' => 'not-a-valid-key',
        'app.url' => 'https://example.com',
    ]);

    artisan('config:validate', ['--files' => ['app']])->assertFailed();
});

it('accepts the production configuration', function (): void {
    app()->detectEnvironment(fn (): string => 'production');
    config([
        'app.debug' => false,
        'app.url' => 'https://example.com',
        'cache.default' => 'database',
        'mail.default' => 'resend',
        'queue.default' => 'database',
        'services.resend.key' => 'resend-key',
        'session.driver' => 'database',
    ]);

    artisan('config:validate')->assertSuccessful();
});

it('skips validation outside production', function (string $environment): void {
    app()->detectEnvironment(fn (): string => $environment);

    artisan('config:validate')->assertSuccessful();
})->with(['local', 'testing']);
