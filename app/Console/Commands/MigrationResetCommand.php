<?php

namespace App\Console\Commands;
use App\Controllers\ComponentController;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrationResetCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ship:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates databases for each component';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->call('ship:down');
        $this->call('ship:up');
    }

}
