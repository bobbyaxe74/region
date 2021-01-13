<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class OvTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ovkey:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the application OV keys';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Check if user is in production mode
        if (App::environment('production')) {

            // Confirm if command should continue
            $this->info("**************************************\n*     Application In Production!     *\n**************************************");
            if ($this->confirm('Do you really wish to run this command?')){

                $this->putEnv('OV_EMAIL_TOKEN', Str::random(32));
                $this->putEnv('OV_CIPHER_TOKEN', Str::random(32));

                return $this->info('Application OV keys were set!');
            } else {

                return $this->info('Command Cancelled!');
            }
        } elseif (App::environment(['local', 'staging'])) {

            $this->putEnv('OV_EMAIL_TOKEN', Str::random(32));
            $this->putEnv('OV_CIPHER_TOKEN', Str::random(32));
            $this->putEnv('APP_KEY', Str::random(32));

            return $this->info('Application OV keys were set!');
        }

        return $this->info('Command Failed!');
    }

    /**
     * Update a specified value in the app's environmental file.
     * 
     * @param string $key
     * @param string $value
     */
    public function putEnv($key, $value)
    {
        $path = App::basePath().'/.env';

        $escaped = preg_quote('='.env($key), '/');

        file_put_contents($path, preg_replace(
            "/^{$key}{$escaped}/m",
            "{$key}={$value}",
            file_get_contents($path)
        ));
    }
}
