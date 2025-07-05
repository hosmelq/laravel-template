<?php

declare(strict_types=1);

use App\Enums\FlashKey;

it('exposes the expected flash keys', function (): void {
    expect(FlashKey::values())->toBe([
        'toast',
    ]);
});
