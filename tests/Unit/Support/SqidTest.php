<?php

declare(strict_types=1);

use App\Support\Sqid;
use Sqids\SqidsInterface;

it('round trips a single integer identifier', function (int $id): void {
    $sqid = resolve(Sqid::class)->encode($id);

    expect($sqid)->toBeString()->toHaveLength(10)
        ->and(resolve(Sqid::class)->decode($sqid))->toBe($id);
})->with([0, 1, 123456789]);

it('rejects malformed and compound identifiers', function (): void {
    $compound = resolve(SqidsInterface::class)->encode([1, 2]);

    expect(resolve(Sqid::class)->decode('invalid!'))->toBeNull()
        ->and(resolve(Sqid::class)->decode(''))->toBeNull()
        ->and(resolve(Sqid::class)->decode($compound))->toBeNull();
});
