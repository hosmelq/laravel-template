<?php

declare(strict_types=1);

namespace App\Enums;

use ArchTech\Enums\Values;

enum TemplateFeature: string
{
    use Values;

    case Authentication = 'authentication';
    case Frontend = 'frontend';
    case Health = 'health';
    case Media = 'media';
    case Organizations = 'organizations';

    public function label(): string
    {
        return match ($this) {
            self::Authentication => 'Authentication',
            self::Frontend => 'Frontend',
            self::Health => 'Health',
            self::Media => 'Media',
            self::Organizations => 'Organizations',
        };
    }
}
