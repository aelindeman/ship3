<?php

namespace App\Console\Commands;
use App\Controllers\ComponentController;

use Illuminate\Console\Command;

class MigrationDownCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ship:down';

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
        if ($this->confirm('Are you sure? This will delete all stored data!')) {

            $this->info('Running component migrations...');
            $components = app(ComponentController::class)->listComponents();

            foreach ($components as $path => $namespace) {
                $name = ComponentController::getComponentName($namespace);
                $migration = $name.'Migration';
                if (app('files')->exists($migrationFile = $path.'/'.$migration.'.php')) {
                    try {
                        include_once($migrationFile);
                        (new $migration)->down();
                        $this->line('  - Uninstalled '.$migration);
                    } catch (\Exception $e) {
                        $this->comment('  - Uninstall failed for '.$migration.':');
                        $this->line('    '.$e->getMessage());
                    }
                }
            }
            $this->info('Complete!');
        } else {
            $this->info('Canceled.');
        }
    }

}
