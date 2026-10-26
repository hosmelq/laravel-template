<?php

declare(strict_types=1);

use function Pest\Laravel\actingAs;
use function Pest\Laravel\freezeSecond;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

pest()
    ->extend(TestCase::class)
    ->beforeEach(function (): void {
        freezeSecond();
    })
    ->in('Feature', 'Integration', 'Unit');

pest()
    ->tia()
    ->always()
    ->baselined()
    ->defaultBranch('main')
    ->locally();

/**
 * @param iterable<Model>|Model $models
 */
function assertDatabaseLockedForUpdate(iterable|Model $models, null|string $connection = null): void
{
    $models = is_iterable($models) ? $models : [$models];
    $expectedLocks = collect($models)
        ->map(function (Model $model) use ($connection): array {
            return [
                'connection' => $connection ?? $model->getConnectionName(),
                'key' => $model->getKey(),
                'keyName' => $model->getKeyName(),
                'table' => $model->getTable(),
            ];
        });
    $queries = [];

    Event::listen(QueryExecuted::class, function (QueryExecuted $query) use (&$queries, $expectedLocks): void {
        if ($expectedLocks->contains(fn (array $expected): bool => $expected['connection'] === null || $query->connectionName === $expected['connection'])) {
            $queries[] = [
                'bindings' => $query->bindings,
                'connection' => $query->connectionName,
                'sql' => $query->sql,
            ];
        }
    });

    test()->beforeApplicationDestroyed(function () use (&$queries, $expectedLocks): void {
        foreach ($expectedLocks as $expected) {
            test()->assertTrue(
                collect($queries)->contains(fn (array $query): bool => ($expected['connection'] === null || $query['connection'] === $expected['connection'])
                    && str_contains($query['sql'], sprintf('"%s"', $expected['table']))
                    && str_contains($query['sql'], sprintf('"%s"', $expected['keyName']))
                    && in_array($expected['key'], $query['bindings'], true)
                    && str_contains($query['sql'], 'for update')),
                sprintf(
                    'Expected a database query%s to lock [%s] for update.',
                    $expected['connection'] === null ? '' : sprintf(' on the [%s] connection', $expected['connection']),
                    $expected['table'],
                )
            );
        }
    });
}

function login(null|Authenticatable $user = null): User
{
    $user ??= User::factory()->createOne();

    actingAs($user);

    return $user;
}
