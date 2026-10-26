<?php

declare(strict_types=1);

use function Pest\Laravel\postJson;

use App\Support\Sqid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

it('decodes configured sqid request fields', function (): void {
    Route::middleware('sqids:parent_id,related_id')
        ->post('/_test', fn (Request $request): array => $request->only([
            'parent_id',
            'related_id',
        ]));

    $sqid = resolve(Sqid::class);

    $response = postJson('/_test', [
        'parent_id' => $sqid->encode(456),
        'related_id' => $sqid->encode(123),
    ]);

    $response->assertExactJson([
        'parent_id' => 456,
        'related_id' => 123,
    ]);
});

it('rejects sqids with different casing', function (): void {
    Route::middleware('sqids:related_id')
        ->post('/_test', fn (Request $request): array => $request->only([
            'related_id',
        ]));

    $sqid = resolve(Sqid::class);
    $uppercaseSqid = Str::upper($sqid->encode(123));

    $response = postJson('/_test', [
        'related_id' => $uppercaseSqid,
    ]);

    $response->assertExactJson([
        'related_id' => 0,
    ]);
});

it('normalizes invalid identifiers without changing nullable or missing fields', function (): void {
    Route::middleware('sqids:missing_id,parent_id,nullable_id,related_id')
        ->post('/_test', fn (Request $request): array => $request->only([
            'missing_id',
            'parent_id',
            'nullable_id',
            'related_id',
        ]));

    $response = postJson('/_test', [
        'parent_id' => 123,
        'nullable_id' => null,
        'related_id' => 'invalid',
    ]);

    $response->assertExactJson([
        'parent_id' => 0,
        'nullable_id' => null,
        'related_id' => 0,
    ]);
});
