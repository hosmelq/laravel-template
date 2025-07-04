<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\Support\Models\TestModel;

it('uses a uuid file name when media is added', function (): void {
    Storage::fake('public');

    $model = TestModel::query()->create();

    $media = $model->addMedia(UploadedFile::fake()->image('photo.png'))
        ->toMediaCollection();

    expect(Str::isUuid(pathinfo($media->file_name, PATHINFO_FILENAME)))->toBeTrue()
        ->and(pathinfo($media->file_name, PATHINFO_EXTENSION))->toBe('png');
});
