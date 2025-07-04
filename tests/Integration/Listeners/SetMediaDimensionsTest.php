<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\UploadedFile;
use Tests\Support\Models\TestModel;

beforeEach(function (): void {
    Relation::enforceMorphMap([
        'test_model' => TestModel::class,
    ]);
});

it('only runs for supported mime types', function (): void {
    $model = TestModel::query()->create();
    $media = $model->addMedia(UploadedFile::fake()->image('image.gif'))
        ->toMediaCollection();

    expect($media->getCustomProperty('height'))->toBeNull()
        ->and($media->getCustomProperty('width'))->toBeNull();
});

it('saves the height and width of the image', function (): void {
    $model = TestModel::query()->create();
    $media = $model->addMedia(UploadedFile::fake()->image('image.jpg', 200, 200))
        ->toMediaCollection();

    expect($media->getCustomProperty('height'))->toBe(200)
        ->and($media->getCustomProperty('width'))->toBe(200);
});
