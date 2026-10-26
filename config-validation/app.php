<?php

declare(strict_types=1);

use AshAllenDesign\ConfigValidator\Services\Rule;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Str;

return [

    Rule::make('debug')->rules(['declined', 'required'])->environments([Rule::ENV_PRODUCTION]),

    Rule::make('key')->rules([
        'required',
        'string',
        function (string $attribute, string $value, Closure $fail): void {
            $key = Str::startsWith($value, 'base64:') ? Str::fromBase64(Str::after($value, 'base64:'), strict: true) : $value;

            if ($key === false || ! Encrypter::supported($key, config()->string('app.cipher'))) {
                $fail('The :attribute cannot initialize encryption with the configured cipher.');
            }
        },
    ])->environments([Rule::ENV_PRODUCTION]),

    Rule::make('url')->rules(['required', 'url:https'])->environments([Rule::ENV_PRODUCTION]),

];
