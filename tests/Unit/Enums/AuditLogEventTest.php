<?php

declare(strict_types=1);

use App\Enums\AuditLogEvent;

it('defines available values', function (): void {
    expect(AuditLogEvent::values())->toEqual([
        'auth.email_verified',
        'auth.lockout',
        'auth.login',
        'auth.login_failed',
        'auth.logout',
        'invitation.accepted',
        'organization.created',
        'organization.updated',
        'user.created',
        'user.updated',
    ]);
});
