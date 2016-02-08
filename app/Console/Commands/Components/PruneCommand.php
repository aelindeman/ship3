<?php

namespace App\Console\Commands\Components;
use App\Controllers\ComponentController;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PruneCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'components:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prunes old data from the database';

    /**
     * Create a new command.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->components = app(ComponentController::class)->run();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $leave = $this->argument('leave');
        $this->info('Will save the newest '.$leave.' entries.');
        $this->info('Pruning old records:');

        foreach ($this->components->getComponents() as $name => $class) {

            // only try if the component has a table
            if ($table = $class->getTable()) {

                app('db')->transaction(function() use ($name, $table, $leave) {
                    $deleted = app('db')->table($table)
                        ->whereNotIn('id', function($query) use ($table, $leave) {
                            $query->select('id')
                                ->from($table)
                                ->orderBy('time', 'desc')
                                ->take($leave);
                        })
                        ->delete();
                    $this->line('  - Deleted '.$deleted.' from '.$name);
                });
            }
        }
        $this->info('Done.');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['leave', InputArgument::OPTIONAL, 'Number of records to leave behind', 4320]
        ];
    }

}
