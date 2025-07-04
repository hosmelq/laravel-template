<?php

declare(strict_types=1);

namespace App\Enums;

use ArchTech\Enums\Values;

enum ToastVariant: string
{
    use Values;

    case Accent = 'accent';
    case Danger = 'danger';
    case Default = 'default';
    case Success = 'success';
    case Warning = 'warning';
}
