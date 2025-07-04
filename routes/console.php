<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('disposable:update')->weekly();

Schedule::command('health:check')->everyMinute();
Schedule::command('health:queue-check-heartbeat')->everyMinute();
Schedule::command('health:schedule-check-heartbeat')->everyMinute();

Schedule::command('model:prune')->daily();
