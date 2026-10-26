<?php

declare(strict_types=1);

use AshAllenDesign\ConfigValidator\Services\Rule;

return [

    Rule::make('default')->rules(['in:resend', 'required'])->environments([Rule::ENV_PRODUCTION]),

    Rule::make('from.address')->rules(['email', 'required'])->environments([Rule::ENV_PRODUCTION]),

];
