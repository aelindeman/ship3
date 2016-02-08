<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\KeyGenerateCommand',
        'App\Console\Commands\MigrationUpCommand',
        'App\Console\Commands\MigrationDownCommand',
        'App\Console\Commands\MigrationResetCommand',
        'App\Console\Commands\PruneCommand',
        'App\Console\Commands\PutCommand',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('ship:put')->everyMinute();
    }
}
