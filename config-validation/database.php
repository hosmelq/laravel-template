<?php

declare(strict_types=1);

use AshAllenDesign\ConfigValidator\Services\Rule;

return [

    Rule::make('default')->rules(['in:pgsql', 'required'])->environments([Rule::ENV_PRODUCTION]),

];
