<?php

declare(strict_types=1);

namespace App\Support\Template;

use App\Enums\TemplateFeature;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class FeatureInstaller
{
    public function __construct(private readonly Filesystem $files)
    {
    }

    /**
     * @param list<TemplateFeature> $features
     */
    public function install(array $features): void
    {
        $features = $this->orderedFeatures($features);

        if ($features === []) {
            return;
        }

        $patches = array_map($this->patchPath(...), $features);

        foreach ($patches as $patch) {
            throw_unless(is_file($patch), RuntimeException::class, 'Patch not found: '.$patch);
        }

        $this->applyPatches($patches);

        $combinationPatches = $this->combinationPatches($features);

        if ($combinationPatches !== []) {
            $this->applyPatches($combinationPatches);
        }

        $this->installDependencies();
        $this->deleteTemplateArtifacts();
    }

    /**
     * @param list<TemplateFeature> $features
     *
     * @return list<TemplateFeature>
     */
    public function pendingFeatures(array $features): array
    {
        return array_values(array_filter(
            $features,
            fn (TemplateFeature $feature): bool => ! $this->isInstalled($feature)
        ));
    }

    /**
     * @param list<string> $patches
     */
    private function applyPatches(array $patches): void
    {
        $this->run(['git', 'apply', '--check', '--unidiff-zero', ...$patches], 'Unable to apply the selected features.');
        $this->run(['git', 'apply', '--unidiff-zero', ...$patches], 'Unable to apply the selected features.');
    }

    /**
     * @param list<TemplateFeature> $features
     *
     * @return list<string>
     */
    private function combinationPatches(array $features): array
    {
        $featureNames = array_column($features, 'value');
        $combinationPatches = [];

        sort($featureNames);

        foreach ($featureNames as $index => $firstFeatureName) {
            foreach (array_slice($featureNames, $index + 1) as $secondFeatureName) {
                $patch = resource_path(sprintf('template/patches/%s+%s.patch', $firstFeatureName, $secondFeatureName));

                if (is_file($patch)) {
                    $combinationPatches[] = $patch;
                }
            }
        }

        return $combinationPatches;
    }

    private function deleteEmptyTemplateDirectories(): bool
    {
        foreach ([
            app_path('Console/Commands'),
            app_path('Console'),
            app_path('Support/Template'),
            base_path('scripts'),
            base_path('tests/Feature/Console'),
            base_path('tests/Integration/Support/Template'),
        ] as $directory) {
            if (! $this->files->isDirectory($directory)) {
                continue;
            }

            if (! $this->files->isEmptyDirectory($directory)) {
                continue;
            }

            if (! $this->files->deleteDirectory($directory)) {
                return false;
            }
        }

        return true;
    }

    private function deleteTemplateArtifacts(): void
    {
        $filesDeleted = $this->files->delete([
            base_path('.github/workflows/template.yml'),
            base_path('README.md'),
            app_path('Console/Commands/InstallTemplateCommand.php'),
            app_path('Enums/TemplateFeature.php'),
            app_path('Support/Template/FeatureInstaller.php'),
            base_path('scripts/test-template-feature-combinations'),
            base_path('tests/Feature/Console/InstallTemplateCommandTest.php'),
            base_path('tests/Integration/Support/Template/FeatureInstallerTest.php'),
            base_path('tests/Support/TemplateFeatureCombinations.php'),
            base_path('tests/Unit/Enums/TemplateFeatureTest.php'),
        ]);
        $emptyDirectoriesDeleted = $this->deleteEmptyTemplateDirectories();
        $templateDirectoryDeleted = $this->files->deleteDirectory(resource_path('template'));

        throw_unless(
            $emptyDirectoriesDeleted && $filesDeleted && $templateDirectoryDeleted,
            RuntimeException::class,
            'The template installer files could not be removed.'
        );
    }

    private function installDependencies(): void
    {
        $this->run([
            'composer',
            'update',
            '--minimal-changes',
            '--no-interaction',
            '--no-scripts',
            '--with-all-dependencies',
        ], 'Composer could not install the selected features.', forever: true);

        $this->run([
            'composer',
            'normalize',
            '--no-interaction',
        ], 'Composer could not normalize the composer.json file.', forever: true);

        $this->run([
            PHP_BINARY,
            'artisan',
            'package:discover',
            '--ansi',
        ], 'Laravel could not discover the installed packages.');

        if ($this->files->isFile(base_path('package.json'))) {
            $this->run(['nub', 'install'], 'The frontend dependencies could not be installed.', forever: true);
            $this->run(['nub', 'run', 'build'], 'The frontend assets could not be built.', forever: true);
        }

        if ($this->files->isFile(base_path('.railway/package.json'))) {
            $this->run(
                ['nub', '--cwd', '.railway', 'install'],
                'The Railway dependencies could not be installed.',
                forever: true
            );
        }

        $this->run(['mise', 'run', 'setup:agent'], 'The agent setup could not be completed.', forever: true);
    }

    private function isInstalled(TemplateFeature $feature): bool
    {
        return is_file(match ($feature) {
            TemplateFeature::Authentication => app_path('Providers/FortifyServiceProvider.php'),
            TemplateFeature::Frontend => base_path('package.json'),
            TemplateFeature::Health => config_path('health.php'),
            TemplateFeature::Media => config_path('media-library.php'),
            TemplateFeature::Organizations => app_path('Models/Organization.php'),
            TemplateFeature::Railway => base_path('.railway/railway.ts'),
        });
    }

    /**
     * @param list<TemplateFeature> $features
     *
     * @return list<TemplateFeature>
     */
    private function orderedFeatures(array $features): array
    {
        return array_values(array_filter(
            [
                TemplateFeature::Health,
                TemplateFeature::Media,
                TemplateFeature::Frontend,
                TemplateFeature::Authentication,
                TemplateFeature::Organizations,
                TemplateFeature::Railway,
            ],
            static fn (TemplateFeature $feature): bool => in_array($feature, $features, true)
        ));
    }

    private function patchPath(TemplateFeature $feature): string
    {
        return resource_path(sprintf('template/patches/%s.patch', $feature->value));
    }

    /**
     * @param list<string> $command
     */
    private function run(array $command, string $message, bool $forever = false): void
    {
        $process = Process::path(base_path());
        $result = $forever
            ? $process->forever()->run($command)
            : $process->timeout(60)->run($command);

        if ($result->failed()) {
            throw new RuntimeException(mb_trim($message."\n".$result->errorOutput()));
        }
    }
}
