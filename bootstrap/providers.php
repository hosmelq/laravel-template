<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelServiceProvider;
use App\Providers\SqidsServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelServiceProvider::class,
    SqidsServiceProvider::class,
];
