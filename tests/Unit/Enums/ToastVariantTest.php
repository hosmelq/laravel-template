<?php

declare(strict_types=1);

use App\Enums\ToastVariant;

it('defines available values', function (): void {
    expect(ToastVariant::values())->toEqual([
        'accent',
        'danger',
        'default',
        'success',
        'warning',
    ]);
});
