<?php

declare(strict_types=1);

namespace App\Support\Media;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer;

class FileNamer extends DefaultFileNamer
{
    public function originalFileName(string $fileName): string
    {
        return (string) Str::uuid();
    }
}
