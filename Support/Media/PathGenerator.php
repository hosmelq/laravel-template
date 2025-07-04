<?php

declare(strict_types=1);

namespace App\Support\Media;

use Illuminate\Support\Facades\Config;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class PathGenerator extends DefaultPathGenerator
{
    /**
     * Get the base path for storing media files.
     *
     * This method overrides the default behavior to use the media's UUID
     * as the base path.
     */
    protected function getBasePath(Media $media): string
    {
        $prefix = Config::string('media-library.prefix', '');

        if ($prefix !== '') {
            return $prefix.'/'.$media->uuid;
        }

        return $media->uuid;
    }
}
