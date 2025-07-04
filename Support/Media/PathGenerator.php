<?php

declare(strict_types=1);

namespace App\Support\Media;

use Illuminate\Support\Facades\Config;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class PathGenerator extends DefaultPathGenerator
{
    protected function getBasePath(Media $media): string
    {
        $prefix = Config::string('media-library.prefix', '');

        if ($prefix !== '') {
            return $prefix.'/'.$media->uuid;
        }

        return $media->uuid;
    }
}
