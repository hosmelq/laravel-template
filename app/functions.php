<?php

declare(strict_types=1);

namespace App;

use App\Enums\FlashKey;
use App\Enums\ToastVariant;
use Inertia\Inertia;

/**
 * Translate the given message.
 *
 * This is a type-safe wrapper around Laravel's __() helper.
 * The core helper can produce string|array|null,
 * so we force it to string to keep static analysis happy.
 *
 * @param array<string, null|bool|float|int|string> $replace
 */
function __(string $key, array $replace = [], null|string $locale = null): string
{
    $message = trans($key, $replace, $locale);

    assert(is_string($message));

    return $message;
}

/**
 * Flash a toast notification to the session.
 */
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
    ], static fn (null|int|string $value): bool => ! is_null($value)));
}
