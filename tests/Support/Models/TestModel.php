<?php

declare(strict_types=1);

namespace Tests\Support\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Table(name: 'test_models', timestamps: false)]
class TestModel extends Model implements HasMedia
{
    use InteractsWithMedia;
}
