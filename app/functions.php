<?php

declare(strict_types=1);

namespace App;

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
