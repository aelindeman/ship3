<?php

namespace App\Console\Commands\Users;
use App\Controllers\ComponentController;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class UserResetKeyCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets a user\'s key';

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
