<?php

declare(strict_types=1);

use App\Enums\FlashKey;

it('defines available values', function (): void {
    expect(FlashKey::values())->toEqual([
        'toast',
    ]);
});
