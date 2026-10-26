<?php

declare(strict_types=1);

namespace Tests\Support\Models;

use App\Models\Concerns\HasSqid;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table(name: 'test_models', timestamps: false)]
class TestModel extends Model
{
    use HasSqid;
    use SoftDeletes;

    /**
     * @return HasMany<TestModel, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
