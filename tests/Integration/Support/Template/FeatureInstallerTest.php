<?php

declare(strict_types=1);

use App\Enums\TemplateFeature;
use App\Support\Template\FeatureInstaller;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Process\FakeProcessResult;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;

it('applies every selected patch as one group and removes the installer after agent setup', function (): void {
    $commands = [];
    $operations = [];

    Process::fake(function (PendingProcess $process) use (&$commands, &$operations): FakeProcessResult {
        $commands[] = $process->command;
        $operations[] = 'process:'.implode(' ', $process->command);

        return Process::result();
    });

    $files = $this->mock(Filesystem::class);
    $files->expects('delete')
        ->with([
            base_path('README.md'),
            app_path('Console/Commands/InstallTemplateCommand.php'),
            app_path('Enums/TemplateFeature.php'),
            app_path('Support/Template/FeatureInstaller.php'),
            base_path('scripts/test-template-feature-combinations'),
            base_path('tests/Feature/Console/InstallTemplateCommandTest.php'),
            base_path('tests/Integration/Support/Template/FeatureInstallerTest.php'),
            base_path('tests/Support/TemplateFeatureCombinations.php'),
            base_path('tests/Unit/Enums/TemplateFeatureTest.php'),
        ])
        ->andReturnUsing(function () use (&$operations): bool {
            $operations[] = 'delete:files';

            return true;
        });
    $files->expects('isDirectory')
        ->times(6)
        ->andReturnTrue();
    $files->expects('isEmptyDirectory')
        ->times(6)
        ->andReturnTrue();
    $files->expects('deleteDirectory')
        ->with(
            Mockery::on(fn (string $directory): bool => in_array($directory, [
                app_path('Console/Commands'),
                app_path('Console'),
                app_path('Support/Template'),
                base_path('scripts'),
                base_path('tests/Feature/Console'),
                base_path('tests/Integration/Support/Template'),
            ], true))
        )
        ->times(6)
        ->andReturnTrue();
    $files->expects('deleteDirectory')
        ->with(resource_path('template'))
        ->andReturnUsing(function () use (&$operations): bool {
            $operations[] = 'delete:directory';

            return true;
        });

    resolve(FeatureInstaller::class)->install(TemplateFeature::cases());

    expect($commands)->toEqual([
        [
            'git',
            'apply',
            '--check',
            '--unidiff-zero',
            resource_path('template/patches/health.patch'),
            resource_path('template/patches/media.patch'),
            resource_path('template/patches/frontend.patch'),
            resource_path('template/patches/authentication.patch'),
            resource_path('template/patches/organizations.patch'),
        ],
        [
            'git',
            'apply',
            '--unidiff-zero',
            resource_path('template/patches/health.patch'),
            resource_path('template/patches/media.patch'),
            resource_path('template/patches/frontend.patch'),
            resource_path('template/patches/authentication.patch'),
            resource_path('template/patches/organizations.patch'),
        ],
        [
            'composer',
            'update',
            '--minimal-changes',
            '--no-interaction',
            '--no-scripts',
            '--with-all-dependencies',
        ],
        [
            PHP_BINARY,
            'artisan',
            'package:discover',
            '--ansi',
        ],
        ['composer', 'agent:setup'],
    ]);

    expect(array_slice($operations, -3))->toEqual([
        'process:composer agent:setup',
        'delete:files',
        'delete:directory',
    ]);
});

it('configures agent setup for non-interactive execution', function (): void {
    $composer = json_decode(
        file_get_contents(base_path('composer.json')),
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    expect($composer['scripts']['agent:setup'])->toContain('nubx -y skills experimental_install');
});

it('preserves non-empty directories when removing the installer', function (): void {
    Process::fake();

    $files = $this->mock(Filesystem::class);
    $files->expects('delete')
        ->once()
        ->andReturnTrue();
    $files->expects('isDirectory')
        ->times(6)
        ->andReturnTrue();
    $files->expects('isEmptyDirectory')
        ->times(6)
        ->andReturnFalse();
    $files->expects('deleteDirectory')
        ->with(resource_path('template'))
        ->once()
        ->andReturnTrue();

    resolve(FeatureInstaller::class)->install([
        TemplateFeature::Health,
    ]);
});

it('preserves the installer when agent setup fails', function (): void {
    Process::fake(function (PendingProcess $process): FakeProcessResult {
        if ($process->command === ['composer', 'agent:setup']) {
            return Process::result(
                errorOutput: 'Agent setup failed.',
                exitCode: 1
            );
        }

        return Process::result();
    });

    $files = $this->mock(Filesystem::class);
    $files->shouldNotReceive('delete');
    $files->shouldNotReceive('deleteDirectory');

    expect(fn () => resolve(FeatureInstaller::class)->install([
        TemplateFeature::Health,
    ]))->toThrow(RuntimeException::class, "The agent setup could not be completed.\nAgent setup failed.");
});

it('does not add unselected features to organizations', function (): void {
    expect(resolve(FeatureInstaller::class)->pendingFeatures([
        TemplateFeature::Organizations,
    ]))->toEqual([
        TemplateFeature::Organizations,
    ]);
});
