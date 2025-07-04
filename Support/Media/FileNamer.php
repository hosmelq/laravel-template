<?php

declare(strict_types=1);

namespace App\Support\Media;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer;

class FileNamer extends DefaultFileNamer
{
    /**
     * Generate a unique filename for the original file.
     *
     * This method overrides the parent class method to generate a UUID
     * as the filename, regardless of the original filename.
     */
    public function originalFileName(string $fileName): string
    {
        return (string) Str::uuid();
    }
}
