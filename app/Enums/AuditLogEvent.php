<?php

declare(strict_types=1);

namespace App\Enums;

use ArchTech\Enums\Values;

enum AuditLogEvent: string
{
    use Values;

    case AuthEmailVerified = 'auth.email_verified';
    case AuthLockout = 'auth.lockout';
    case AuthLogin = 'auth.login';
    case AuthLoginFailed = 'auth.login_failed';
    case AuthLogout = 'auth.logout';
    case InvitationAccepted = 'invitation.accepted';
    case OrganizationCreated = 'organization.created';
    case OrganizationUpdated = 'organization.updated';
    case UserCreated = 'user.created';
    case UserUpdated = 'user.updated';
}
