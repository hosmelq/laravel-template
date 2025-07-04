<?php

declare(strict_types=1);

namespace App\Listeners;

use function Sentry\captureException;

use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;
use Throwable;

class SetMediaDimensions
{
    public function handle(MediaHasBeenAddedEvent $event): void
    {
        try {
            if (! in_array(Str::lower($event->media->mime_type), ['image/jpeg', 'image/jpg', 'image/png'], true)) {
                return;
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->decodeStream($event->media->stream());

            $event->media->setCustomProperty('height', $image->height());
            $event->media->setCustomProperty('width', $image->width());

            $event->media->save();
        } catch (Throwable $e) {
            captureException($e);
        }
    }
}
