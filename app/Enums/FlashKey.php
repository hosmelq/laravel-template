<?php

declare(strict_types=1);

namespace App\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Values;

/**
 * @method static string Toast()
 */
enum FlashKey: string
{
    use InvokableCases;
    use Values;

    case Toast = 'toast';
}
