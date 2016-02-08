<?php

namespace App\Console\Commands\Components;
use App\Controllers\ComponentController;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MigrationDownCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'components:uninstall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop databases for each component';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if ($this->option('yes') or
        	$this->confirm('Are you sure? This will delete all stored data!')) {

            $this->info('Rolling back component tables:');
            $components = app(ComponentController::class)->listComponents();

            foreach ($components as $path => $namespace) {
                $name = ComponentController::getComponentName($namespace);
                $migration = $name.'Migration';
                if (app('files')->exists($migrationFile = $path.'/'.$migration.'.php')) {
                    try {
                        include_once($migrationFile);
                        (new $migration)->down();
                        $this->line('  - Rolled back '.$migration);
                    } catch (\Exception $e) {
                        $this->comment('  - Rollback failed for '.$migration.':');
                        $this->line('    '.$e->getMessage());
                    }
                }
            }
            $this->info('Complete!');
        } else {
            $this->info('Canceled.');
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['yes', 'y', InputOption::VALUE_NONE, 'Delete without asking']
        ];
    }

}
