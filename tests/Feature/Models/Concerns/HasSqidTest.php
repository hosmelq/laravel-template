<?php

declare(strict_types=1);

use function Pest\Laravel\get;

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Tests\Support\Models\TestModel;

it('resolves computed route keys and rejects internal and invalid identifiers', function (): void {
    $model = TestModel::query()->create();

    Route::middleware(SubstituteBindings::class)
        ->get('/_test/{model}', fn (TestModel $model): string => $model->sqid);

    get('/_test/'.$model->sqid)->assertOk()->assertSeeText($model->sqid);
    get('/_test/'.$model->id)->assertNotFound();
    get('/_test/invalid!')->assertNotFound();
});

it('preserves explicit binding fields', function (): void {
    $model = TestModel::query()->create();

    Route::middleware(SubstituteBindings::class)
        ->get('/_test/{model:id}', fn (TestModel $model): int => $model->id);

    get('/_test/'.$model->id)->assertOk();
});

it('keeps child bindings scoped to their parent', function (): void {
    $parent = TestModel::query()->create();
    $otherParent = TestModel::query()->create();
    $child = TestModel::query()->create(['parent_id' => $parent->id]);

    Route::middleware(SubstituteBindings::class)
        ->get('/_test/{model}/children/{child}', fn (TestModel $model, TestModel $child): string => $child->sqid)
        ->scopeBindings();

    get('/_test/'.$parent->sqid.'/children/'.$child->sqid)->assertOk();
    get('/_test/'.$otherParent->sqid.'/children/'.$child->sqid)->assertNotFound();
});

it('honors soft deleted binding settings', function (): void {
    $model = TestModel::query()->create(['deleted_at' => now()]);

    Route::middleware(SubstituteBindings::class)
        ->get('/_test/{model}', fn (TestModel $model): string => $model->sqid);

    Route::middleware(SubstituteBindings::class)
        ->get('/_trashed/{model}', fn (TestModel $model): string => $model->sqid)
        ->withTrashed();

    get('/_test/'.$model->sqid)->assertNotFound();
    get('/_trashed/'.$model->sqid)->assertOk();
});
