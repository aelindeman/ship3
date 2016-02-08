<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShipInstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ship:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs Ship';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->call('key:generate');
        $this->call('migrate:install');
        $this->call('migrate:refresh');
        $this->call('components:install');
    }
}
