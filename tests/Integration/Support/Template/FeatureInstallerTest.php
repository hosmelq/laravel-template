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

    Process::fake(
        function (PendingProcess $process) use (&$commands, &$operations): FakeProcessResult {
            $commands[] = $process->command;
            $operations[] = 'process:'.implode(' ', $process->command);

            return Process::result();
        },
    );

    $files = $this->mock(Filesystem::class);
    $files->expects('isFile')->with(base_path('package.json'))->andReturnFalse();
    $files->expects('isFile')->with(base_path('.railway/package.json'))->andReturnFalse();
    $files->expects('delete')
        ->with([
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
        ])
        ->andReturnUsing(function () use (&$operations): bool {
            $operations[] = 'delete:files';

            return true;
        });
    $files->expects('isDirectory')->times(6)->andReturnTrue();
    $files->expects('isEmptyDirectory')->times(6)->andReturnTrue();
    $files->expects('deleteDirectory')
        ->with(
            Mockery::on(fn (string $directory): bool => in_array($directory, [
                app_path('Console/Commands'),
                app_path('Console'),
                app_path('Support/Template'),
                base_path('scripts'),
                base_path('tests/Feature/Console'),
                base_path('tests/Integration/Support/Template'),
            ], true)),
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
            resource_path('template/patches/railway.patch'),
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
            resource_path('template/patches/railway.patch'),
        ],
        [
            'git',
            'apply',
            '--check',
            '--unidiff-zero',
            resource_path('template/patches/authentication+frontend.patch'),
            resource_path('template/patches/authentication+organizations.patch'),
            resource_path('template/patches/frontend+organizations.patch'),
            resource_path('template/patches/frontend+railway.patch'),
        ],
        [
            'git',
            'apply',
            '--unidiff-zero',
            resource_path('template/patches/authentication+frontend.patch'),
            resource_path('template/patches/authentication+organizations.patch'),
            resource_path('template/patches/frontend+organizations.patch'),
            resource_path('template/patches/frontend+railway.patch'),
        ],
        [
            'git',
            'apply',
            '--check',
            '--unidiff-zero',
            resource_path('template/patches/authentication+frontend+organizations.patch'),
        ],
        [
            'git',
            'apply',
            '--unidiff-zero',
            resource_path('template/patches/authentication+frontend+organizations.patch'),
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
            'composer',
            'normalize',
            '--no-interaction',
        ],
        [
            PHP_BINARY,
            'artisan',
            'package:discover',
            '--ansi',
        ],
        ['mise', 'run', 'setup:agent'],
    ])
        ->and(array_slice($operations, -3))->toEqual([
            'process:mise run setup:agent',
            'delete:files',
            'delete:directory',
        ]);
});

it('applies combination patches only when every combined feature is selected', function (): void {
    $commands = [];

    Process::fake(function (PendingProcess $process) use (&$commands): FakeProcessResult {
        $commands[] = $process->command;

        return Process::result();
    });

    $files = $this->mock(Filesystem::class);
    $files->expects('isFile')->with(base_path('package.json'))->andReturnFalse();
    $files->expects('isFile')->with(base_path('.railway/package.json'))->andReturnFalse();
    $files->expects('delete')->once()->andReturnTrue();
    $files->expects('isDirectory')->times(6)->andReturnFalse();
    $files->expects('deleteDirectory')->with(resource_path('template'))->once()->andReturnTrue();

    resolve(FeatureInstaller::class)->install([
        TemplateFeature::Frontend,
        TemplateFeature::Authentication,
    ]);

    expect(array_slice($commands, 1, 3))->toEqual([
        [
            'git',
            'apply',
            '--unidiff-zero',
            resource_path('template/patches/frontend.patch'),
            resource_path('template/patches/authentication.patch'),
        ],
        [
            'git',
            'apply',
            '--check',
            '--unidiff-zero',
            resource_path('template/patches/authentication+frontend.patch'),
        ],
        [
            'git',
            'apply',
            '--unidiff-zero',
            resource_path('template/patches/authentication+frontend.patch'),
        ],
    ]);
});

it('preserves non-empty directories when removing the installer', function (): void {
    Process::fake();

    $files = $this->mock(Filesystem::class);
    $files->expects('isFile')->with(base_path('package.json'))->andReturnFalse();
    $files->expects('isFile')->with(base_path('.railway/package.json'))->andReturnFalse();
    $files->expects('delete')->once()->andReturnTrue();
    $files->expects('isDirectory')->times(6)->andReturnTrue();
    $files->expects('isEmptyDirectory')->times(6)->andReturnFalse();
    $files->expects('deleteDirectory')->with(resource_path('template'))->once()->andReturnTrue();

    resolve(FeatureInstaller::class)->install([
        TemplateFeature::Health,
    ]);
});

it('preserves the installer when agent setup fails', function (): void {
    Process::fake(function (PendingProcess $process): FakeProcessResult {
        if ($process->command === ['mise', 'run', 'setup:agent']) {
            return Process::result(
                errorOutput: 'Agent setup failed.',
                exitCode: 1,
            );
        }

        return Process::result();
    });

    $files = $this->mock(Filesystem::class);
    $files->expects('isFile')->with(base_path('package.json'))->andReturnFalse();
    $files->expects('isFile')->with(base_path('.railway/package.json'))->andReturnFalse();
    $files->shouldNotReceive('delete');
    $files->shouldNotReceive('deleteDirectory');

    expect(fn () => resolve(FeatureInstaller::class)->install([
        TemplateFeature::Health,
    ]))->toThrow(
        RuntimeException::class,
        "The agent setup could not be completed.\nAgent setup failed.",
    );
});

it('does not add unselected features to organizations', function (): void {
    expect(resolve(FeatureInstaller::class)->pendingFeatures([
        TemplateFeature::Organizations,
    ]))->toEqual([
        TemplateFeature::Organizations,
    ]);
});

it('builds frontend assets before agent setup and installer cleanup', function (): void {
    $commands = [];

    Process::fake(function (PendingProcess $process) use (&$commands): FakeProcessResult {
        $commands[] = $process->command;

        return Process::result();
    });

    $files = $this->mock(Filesystem::class);
    $files->expects('isFile')->with(base_path('package.json'))->andReturnTrue();
    $files->expects('isFile')->with(base_path('.railway/package.json'))->andReturnFalse();
    $files->expects('delete')->once()->andReturnTrue();
    $files->expects('isDirectory')->times(6)->andReturnFalse();
    $files->expects('deleteDirectory')->with(resource_path('template'))->once()->andReturnTrue();

    resolve(FeatureInstaller::class)->install([TemplateFeature::Frontend]);

    expect(array_slice($commands, -3))->toEqual([
        ['nub', 'install'],
        ['nub', 'run', 'build'],
        ['mise', 'run', 'setup:agent'],
    ]);
});

it('installs Railway dependencies before agent setup', function (): void {
    $commands = [];

    Process::fake(function (PendingProcess $process) use (&$commands): FakeProcessResult {
        $commands[] = $process->command;

        return Process::result();
    });

    $files = $this->mock(Filesystem::class);
    $files->expects('isFile')->with(base_path('package.json'))->andReturnFalse();
    $files->expects('isFile')->with(base_path('.railway/package.json'))->andReturnTrue();
    $files->expects('delete')->once()->andReturnTrue();
    $files->expects('isDirectory')->times(6)->andReturnFalse();
    $files->expects('deleteDirectory')->with(resource_path('template'))->once()->andReturnTrue();

    resolve(FeatureInstaller::class)->install([TemplateFeature::Railway]);

    expect(array_slice($commands, -2))->toEqual([
        ['nub', '--cwd', '.railway', 'install'],
        ['mise', 'run', 'setup:agent'],
    ]);
});

it('preserves the installer when the frontend build fails', function (): void {
    Process::fake(function (PendingProcess $process): FakeProcessResult {
        return $process->command === ['nub', 'run', 'build']
            ? Process::result(errorOutput: 'Build failed.', exitCode: 1)
            : Process::result();
    });

    $files = $this->mock(Filesystem::class);
    $files->expects('isFile')->with(base_path('package.json'))->andReturnTrue();
    $files->shouldNotReceive('delete');
    $files->shouldNotReceive('deleteDirectory');

    expect(fn () => resolve(FeatureInstaller::class)->install([TemplateFeature::Frontend]))
        ->toThrow(
            RuntimeException::class,
            "The frontend assets could not be built.\nBuild failed.",
        );
});
