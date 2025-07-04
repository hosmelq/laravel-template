<?php

declare(strict_types=1);

namespace App\Providers;

use Spatie\LaravelTypeScriptTransformer\TypeScriptTransformerApplicationServiceProvider as BaseTypeScriptTransformerServiceProvider;
use Spatie\TypeScriptTransformer\Transformers\EnumTransformer;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfigFactory;
use Spatie\TypeScriptTransformer\Writers\FlatModuleWriter;

class TypeScriptTransformerServiceProvider extends BaseTypeScriptTransformerServiceProvider
{
    /**
     * Generate PHP enums as TypeScript union types for the frontend.
     */
    protected function configure(TypeScriptTransformerConfigFactory $config): void
    {
        $config
            ->transformer(EnumTransformer::class)
            ->transformDirectories(app_path('Enums'))
            ->outputDirectory(resource_path('js/types'))
            ->writer(new FlatModuleWriter('generated/enums.ts'));
    }
}
