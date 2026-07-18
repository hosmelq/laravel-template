<?php

declare(strict_types=1);

namespace App;

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
