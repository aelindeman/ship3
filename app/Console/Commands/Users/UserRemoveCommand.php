<?php

namespace App\Console\Commands\Users;
use App\Controllers\ComponentController;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class UserRemoveCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes a user';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Username', null]
        ];
    }

}
