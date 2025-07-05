<?php

declare(strict_types=1);

use App\Enums\ToastVariant;

it('exposes the expected toast variants', function (): void {
    expect(ToastVariant::values())->toBe([
        'accent',
        'danger',
        'default',
        'success',
        'warning',
    ]);
});
