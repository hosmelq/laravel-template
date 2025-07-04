<?php

declare(strict_types=1);

namespace App;

use App\Enums\FlashKey;
use App\Enums\ToastVariant;
use Inertia\Inertia;

/**
 * Wraps Laravel's translation helper so the return type stays narrowed to string.
 *
 * @param array<string, null|bool|float|int|string> $replace
 */
function __(string $key, array $replace = [], null|string $locale = null): string
{
    $message = trans($key, $replace, $locale);

    assert(is_string($message));

    return $message;
}

function toast(
    string $title,
    null|string $description = null,
    ToastVariant $variant = ToastVariant::Success,
    int $timeout = 5
): void {
    Inertia::flash(FlashKey::Toast(), array_filter([
        'description' => $description,
        'timeout' => $timeout * 1000,
        'title' => $title,
        'variant' => $variant->value,
    ], fn (null|int|string $value): bool => $value !== null));
}
