<?php

declare(strict_types=1);

use AshAllenDesign\ConfigValidator\Services\Rule;

return [

    Rule::make('resend.key')->rules(['required', 'string'])->environments([Rule::ENV_PRODUCTION]),

];
