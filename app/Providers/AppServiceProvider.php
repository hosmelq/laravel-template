<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Sentry\Laravel\Integration;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureCommands();
        $this->configureDates();
        $this->configureModels();
        $this->configurePasswordValidation();
        $this->configureRateLimiters();
        $this->configureResources();
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

        Relation::enforceMorphMap([]);

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
     * Configure Vite.
     */
    private function configureVite(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
