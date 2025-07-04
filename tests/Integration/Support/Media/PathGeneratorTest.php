<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\Support\Models\TestModel;

it('uses the media uuid as the base path', function (): void {
    Storage::fake('public');

    $model = TestModel::query()->create();

    $media = $model->addMedia(UploadedFile::fake()->image('image.jpg'))
        ->toMediaCollection();

    $path = $media->getPathRelativeToRoot();

    expect(Str::startsWith($path, $media->uuid.'/'))->toBeTrue();
});

it('includes the configured prefix before the media uuid path', function (): void {
    Storage::fake('public');

    config(['media-library.prefix' => 'uploads']);

    $model = TestModel::query()->create();

    $media = $model->addMedia(UploadedFile::fake()->image('image.jpg'))
        ->toMediaCollection();

    $path = $media->getPathRelativeToRoot();

    expect(Str::startsWith($path, 'uploads/'.$media->uuid.'/'))->toBeTrue();
});
