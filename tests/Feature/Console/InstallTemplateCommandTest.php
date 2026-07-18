<?php

declare(strict_types=1);

use function Pest\Laravel\artisan;

use App\Support\Template\FeatureInstaller;
use Illuminate\Support\Facades\Artisan;
use Tests\Support\TemplateFeatureCombinations;

it('installs every selected prompt feature combination', function (array $selectedFeatures): void {
    $installer = $this->mock(FeatureInstaller::class);
    $installer->expects('pendingFeatures')
        ->with($selectedFeatures)
        ->andReturn([]);

    artisan('template:install')
        ->expectsChoice(
            'Which features do you want to install?',
            array_column($selectedFeatures, 'value'),
            [
                'authentication',
                'frontend',
                'health',
                'media',
                'organizations',
                'Authentication',
                'Frontend',
                'Health',
                'Media',
                'Organizations',
            ],
            strict: true
        )
        ->expectsOutputToContain('The selected features are already installed.')
        ->assertSuccessful();
})->with(TemplateFeatureCombinations::all());

it('selects features exclusively through prompts', function (): void {
    $definition = Artisan::all()['template:install']->getDefinition();

    expect($definition->hasArgument('features'))->toBeFalse()
        ->and($definition->hasOption('no-dependencies'))->toBeFalse();
});
