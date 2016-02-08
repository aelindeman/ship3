<?php

namespace App\Console\Commands;
use App\Controllers\ComponentController;

use Illuminate\Console\Command;

class PutCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ship:put';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Writes current data to the database';

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
        $time = ['time' => date('c')];
        $log = collect($time);

        foreach ($this->components->getComponents() as $name => $class) {

            // only try if the component has a table
            if ($table = $class->getTable()) {
                app('db')->beginTransaction();

                // get component data and put it in the database
                try {
                    $data = $class->run();
                    app('db')->table($table)->insert($data);
                    app('db')->commit();
                } catch (\Exception $e) {
                    app('db')->rollBack();
                    $errors[$name] = $e;
                    $this->comment('Put for '.$name.' failed: '.$e->getMessage());
                }
            }
        }

        $this->info('Finished'.(isset($errors) ? ', with errors.' : '.'));
    }
}
