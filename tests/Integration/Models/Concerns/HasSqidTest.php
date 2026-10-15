<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Support\Models\TestModel;

it('computes a public identifier without persisting it', function (): void {
    $model = TestModel::query()->create();

    expect($model->sqid)->toBeString()->toHaveLength(10)
        ->and($model->getAttributes())->not->toHaveKey('sqid')
        ->and($model->getRouteKeyName())->toBe('sqid');
});

it('finds models by their computed identifier', function (): void {
    $model = TestModel::query()->create();

    expect(TestModel::findBySqid($model->sqid)?->is($model))->toBeTrue()
        ->and(TestModel::findOrFailBySqid($model->sqid)->is($model))->toBeTrue()
        ->and(TestModel::findBySqid('invalid!'))->toBeNull();
});

it('honors a model route key attribute', function (): void {
    $model = new #[RouteKey('id')] class extends TestModel {};

    expect($model->getRouteKeyName())->toBe('id');
});

it('rejects computing identifiers for unsaved models', function (): void {
    expect(fn (): string => new TestModel()->sqid)->toThrow(LogicException::class);
});

it('throws when a public identifier cannot be found', function (): void {
    expect(fn (): TestModel => TestModel::findOrFailBySqid('invalid!'))
        ->toThrow(ModelNotFoundException::class);
});
