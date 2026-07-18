<?php

declare(strict_types=1);

use App\Enums\TemplateFeature;

it('defines available values', function (): void {
    expect(TemplateFeature::values())->toEqual([
        'authentication',
        'frontend',
        'health',
        'media',
        'organizations',
    ]);
});

it('defines labels', function (TemplateFeature $feature, string $label): void {
    expect($feature->label())->toBe($label);
})->with([
    'authentication' => [TemplateFeature::Authentication, 'Authentication'],
    'frontend' => [TemplateFeature::Frontend, 'Frontend'],
    'health' => [TemplateFeature::Health, 'Health'],
    'media' => [TemplateFeature::Media, 'Media'],
    'organizations' => [TemplateFeature::Organizations, 'Organizations'],
]);
