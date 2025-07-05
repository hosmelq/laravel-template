<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\FlashKey;
use App\Enums\ToastVariant;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Sentry\Laravel\Integration;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureCommands();
        $this->configureDates();
        $this->configureModels();
        $this->configurePasswordValidation();
        $this->configureRateLimiters();
        $this->configureResources();
        $this->configureRouteMacros();
        $this->configureTestingProviders();
        $this->configureUrls();
        $this->configureVite();
    }

    /**
     * Configure the application's commands.
     */
    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(App::isProduction());
    }

    /**
     * Configure the dates.
     */
    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    /**
     * Configure the models.
     */
    private function configureModels(): void
    {
        Model::automaticallyEagerLoadRelationships();
        Model::shouldBeStrict();
        Model::unguard();

        Relation::enforceMorphMap([
            'user' => User::class,
        ]);

        if (App::isProduction()) {
            Model::handleDiscardedAttributeViolationUsing(
                Integration::discardedAttributeViolationReporter()
            );
            Model::handleLazyLoadingViolationUsing(
                Integration::lazyLoadingViolationReporter()
            );
            Model::handleMissingAttributeViolationUsing(
                Integration::missingAttributeViolationReporter()
            );
        }
    }

    /**
     * Configure the password validation rules.
     */
    private function configurePasswordValidation(): void
    {
        Password::defaults(fn () => app()->isProduction() ? Password::min(8)->uncompromised() : Password::min(8));
    }

    /**
     * Configure the rate limiters.
     */
    private function configureRateLimiters(): void
    {
        RateLimiter::for('api', function (Request $request): Limit {
            return Limit::perMinute(1000)->by($request->user()->id ?? $request->ip());
        });
    }

    /**
     * Configure the JSON resources.
     */
    private function configureResources(): void
    {
        JsonResource::withoutWrapping();
    }

    /**
     * Configure route macros.
     */
    private function configureRouteMacros(): void
    {
        RedirectResponse::macro('toast', function (
            string $title,
            null|string $description = null,
            ToastVariant $variant = ToastVariant::Success,
            int $timeout = 5
        ): RedirectResponse {
            Inertia::flash(FlashKey::Toast(), array_filter([
                'description' => $description,
                'timeout' => $timeout * 1000,
                'title' => $title,
                'variant' => $variant->value,
            ], fn (null|int|string $value): bool => ! is_null($value)));

            return $this;
        });
    }

    /**
     * Configure testing-only service providers.
     */
    private function configureTestingProviders(): void
    {
        if (App::runningUnitTests()) {
            $this->app->register(TestingServiceProvider::class);
        }
    }

    /**
     * Configure the URLs.
     */
    private function configureUrls(): void
    {
        URL::forceHttps(App::isProduction());
    }

    /**
     * Configure Vite.
     */
    private function configureVite(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
