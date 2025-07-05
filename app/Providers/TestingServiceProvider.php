<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\ToastVariant;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;

class TestingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureTestResponseMacros();
    }

    /**
     * Configure test response macros.
     */
    private function configureTestResponseMacros(): void
    {
        TestResponse::macro('assertToast', function (
            string $title,
            null|string $description = null,
            ToastVariant $variant = ToastVariant::Success,
            int $timeout = 5
        ): TestResponse {
            return $this->assertSessionHas('inertia.flash_data.toast', array_filter([
                'description' => $description,
                'timeout' => $timeout * 1000,
                'title' => $title,
                'variant' => $variant->value,
            ], static fn (null|int|string $value): bool => ! is_null($value)));
        });
    }
}
