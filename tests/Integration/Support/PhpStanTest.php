<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

it('rejects every supported type safety violation with the project configuration', function (): void {
    $fixture = sys_get_temp_dir().'/type-perfect-'.Str::uuid().'.php';

    File::copy(base_path('tests/testfiles/type-perfect.php.stub'), $fixture);

    try {
        $result = Process::path(base_path())->env(['PAO_DISABLE' => true])->timeout(60)->run([
            PHP_BINARY,
            base_path('vendor/bin/phpstan'),
            'analyse',
            '--configuration='.base_path('phpstan.neon'),
            '--error-format=json',
            '--no-progress',
            '--memory-limit=1G',
            $fixture,
        ]);

        expect($result->exitCode())->toBe(1);

        $report = json_decode($result->output(), true, 512, JSON_THROW_ON_ERROR);
        $identifiers = collect($report['files'])
            ->flatMap(fn (array $file): array => array_column($file['messages'], 'identifier'))
            ->all();

        expect($identifiers)->toContain(
            'typePerfect.narrowPrivateClassMethodParamType',
            'typePerfect.narrowReturnObjectType',
            'typePerfect.noEmptyOnObject',
            'typePerfect.noIssetOnObject',
            'typePerfect.noMixedMethodCaller',
            'typePerfect.noMixedPropertyFetcher',
            'typePerfect.noParamTypeRemoval',
        );
    } finally {
        File::delete($fixture);
    }
});
