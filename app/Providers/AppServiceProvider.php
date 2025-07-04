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
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Sentry\Laravel\Integration;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Facades\Health;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureCommands();
        $this->configureDates();
        $this->configureFormRequests();
        $this->configureLaravelHealth();
        $this->configureModels();
        $this->configurePasswordValidation();
        $this->configureRateLimiters();
        $this->configureResources();
        $this->configureRouteMacros();
        $this->configureUrls();
        $this->configureVite();
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands($this->app->isProduction());
    }

    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureFormRequests(): void
    {
        FormRequest::failOnUnknownFields();
    }

    private function configureLaravelHealth(): void
    {
        Health::checks([
            CacheCheck::new(),
            DatabaseCheck::new(),
            DebugModeCheck::new(),
            OptimizedAppCheck::new(),
            QueueCheck::new()->failWhenHealthJobTakesLongerThanMinutes(2),
            ScheduleCheck::new()->heartbeatMaxAgeInMinutes(2),
        ]);
    }

    private function configureModels(): void
    {
        Model::automaticallyEagerLoadRelationships();
        Model::shouldBeStrict();
        Model::unguard();

        Relation::enforceMorphMap([
            'user' => User::class,
        ]);

        if ($this->app->isProduction()) {
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

    private function configurePasswordValidation(): void
    {
        Password::defaults(fn () => app()->isProduction() ? Password::min(8)->uncompromised() : Password::min(8));
    }

    private function configureRateLimiters(): void
    {
        RateLimiter::for('api', function (Request $request): Limit {
            return Limit::perMinute(1000)->by($request->user()->id ?? $request->ip());
        });
    }

    private function configureResources(): void
    {
        JsonResource::withoutWrapping();
    }

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
            ], fn (null|int|string $value): bool => $value !== null));

            return $this;
        });
    }

    private function configureUrls(): void
    {
        URL::forceHttps(! $this->app->environment('local', 'testing'));
    }

    private function configureVite(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
