<?php

namespace App\Console\Commands\Components;

use Illuminate\Console\Command;

class MigrationResetCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'components:reset';

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
        $this->call('components:remove');
        $this->call('components:install');
    }

}
