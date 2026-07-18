<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Sentry\Laravel\Integration;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureCommands();
        $this->configureDates();
        $this->configureFormRequests();
        $this->configureModels();
        $this->configurePasswordValidation();
        $this->configureRateLimiters();
        $this->configureResources();
        $this->configureUrls();
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

    private function configureUrls(): void
    {
        URL::forceHttps(! $this->app->environment('local', 'testing'));
    }
}
