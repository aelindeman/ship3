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
        'App\Console\Commands\Components\MigrationDownCommand',
        'App\Console\Commands\Components\MigrationResetCommand',
        'App\Console\Commands\Components\MigrationUpCommand',
        'App\Console\Commands\Components\PruneCommand',
        'App\Console\Commands\Components\PutCommand',
        'App\Console\Commands\KeyGenerateCommand',
        'App\Console\Commands\ShipInstallCommand',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('components:put')->everyMinute();
    }
}
