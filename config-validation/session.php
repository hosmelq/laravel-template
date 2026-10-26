<?php

declare(strict_types=1);

use AshAllenDesign\ConfigValidator\Services\Rule;

return [

    Rule::make('driver')->rules(['in:database', 'required'])->environments([Rule::ENV_PRODUCTION]),

];
