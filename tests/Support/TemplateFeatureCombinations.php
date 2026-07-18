<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Enums\TemplateFeature;

final class TemplateFeatureCombinations
{
    /**
     * @return array<string, array{list<TemplateFeature>}>
     */
    public static function all(): array
    {
        $combinations = [];
        $features = TemplateFeature::cases();

        for ($mask = 1; $mask < (1 << count($features)); ++$mask) {
            $selectedFeatures = [];

            foreach ($features as $index => $feature) {
                if (($mask & (1 << $index)) !== 0) {
                    $selectedFeatures[] = $feature;
                }
            }

            $combinations[implode(' and ', array_column($selectedFeatures, 'value'))] = [$selectedFeatures];
        }

        ksort($combinations);

        return $combinations;
    }
}
