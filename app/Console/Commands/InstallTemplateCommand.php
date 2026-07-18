<?php

declare(strict_types=1);

namespace App\Console\Commands;

use function Laravel\Prompts\multiselect;

use App\Enums\TemplateFeature;
use App\Support\Template\FeatureInstaller;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use RuntimeException;

#[Description('Install optional template features.')]
#[Signature('template:install')]
class InstallTemplateCommand extends Command
{
    public function handle(FeatureInstaller $installer): int
    {
        $features = $this->selectedFeatures();

        try {
            $pending = $installer->pendingFeatures($features);

            if ($pending === []) {
                $this->components->info('The selected features are already installed.');

                return self::SUCCESS;
            }

            $installer->install($pending);
        } catch (RuntimeException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info('Installed: '.implode(', ', array_map(
            static fn (TemplateFeature $feature): string => $feature->label(),
            $pending
        )));

        return self::SUCCESS;
    }

    /**
     * @return list<TemplateFeature>
     */
    private function selectedFeatures(): array
    {
        $options = [];

        foreach (TemplateFeature::cases() as $feature) {
            $options[$feature->value] = $feature->label();
        }

        $selected = multiselect(
            label: 'Which features do you want to install?',
            options: $options,
            required: true
        );

        return array_values(array_map(TemplateFeature::from(...), $selected));
    }
}
