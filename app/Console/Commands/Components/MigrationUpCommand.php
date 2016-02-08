<?php

namespace App\Console\Commands\Components;
use App\Controllers\ComponentController;

use Illuminate\Console\Command;

class MigrationUpCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'components:install';

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
        $this->info('Installing component tables:');
        $components = app(ComponentController::class)->listComponents();

        foreach ($components as $path => $namespace) {
            $name = ComponentController::getComponentName($namespace);
            $migration = $name.'Migration';
            if (app('files')->exists($migrationFile = $path.'/'.$migration.'.php')) {
                try {
                    include_once($migrationFile);
                    (new $migration)->up();
                    $this->line('  - Installed '.$migration);
                } catch (\Exception $e) {
                    $this->comment('  - Install failed for '.$migration.':');
                    $this->line('    '.$e->getMessage());
                }
            }
        }

        $this->info('Complete!');
    }

}
