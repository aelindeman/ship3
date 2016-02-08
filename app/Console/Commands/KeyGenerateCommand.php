<?php

namespace App\Console\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class KeyGenerateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'key:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the application key';

    /**
     * Create a new key generator command.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        list($path, $contents) = $this->getDotEnvFile();

        $newKey = Str::random(32);

        // find existing key, uncomment if necessary, and replace with new key
        if (preg_match('/^([#;]?\s*)(APP_KEY=)(.*?)$/m', $contents, $match)) {
            $contents = str_replace($match[0], $match[2].$newKey, $contents);
        } else {
            $contents = 'APP_KEY='.$newKey.PHP_EOL.$contents;
        }

        $this->files->put($path, $contents);
        $this->info('Created new key.');
    }

    /**
     * Get the environment file.
     *
     * @return array
     */
    protected function getDotEnvFile()
    {
        $path = base_path('.env');

        try {
            // try to fetch existing .env file
            $contents = $this->files->get($path);
        } catch (\Exception $e) {
            try {
                // copy .env.example to .env and try again
                $this->files->copy(base_path('.env.example'), $path);
                $contents = $this->files->get($path);
                $this->comment('Created new environment file from .env.example; will only set new key.');
            } catch (FileNotFoundException $e) {
                // there's no .env.example, so make a blank .env file
                $this->files->put($path, $contents = '');
                $this->comment('Created new blank environment file.');
            } catch (\Exception $e) {
                // can't do anything
                throw $e;
            }
        }

        return [$path, $contents];
    }
}
