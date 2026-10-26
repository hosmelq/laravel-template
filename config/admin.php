<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Users
    |--------------------------------------------------------------------------
    |
    | Define a small allowlist of admin users without persisting the role in
    | the database. Provide a comma-separated list of email addresses using
    | the ADMIN_EMAILS environment variable.
    |
    */

    'emails' => [
        ...array_filter(
            explode(',', (string) env('ADMIN_EMAILS', '')),
            static fn (string $email): bool => $email !== '',
        ),
    ],

];
