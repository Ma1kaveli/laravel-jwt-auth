<?php

namespace JWTAuth\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class JWTSecretGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:secret';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for generate key for JWT token';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Artisan::call("config:cache");
        $secretKey = bin2hex(random_bytes(32));
        $key = 'JWT_PRIVATE';
        $this->info("Install new private JWT key: $secretKey");
        file_put_contents(App::environmentFilePath(), str_replace(
            $key . '=' . Config::get('jwt.private'),
            $key . '=' . $secretKey,
            file_get_contents(App::environmentFilePath())
        ));
        $configKey = 'jwt.private';

        Config::set($configKey, $secretKey);

        // Reload the cached config
        if (file_exists(App::getCachedConfigPath())) {
            Artisan::call("config:cache");
        }
    }
}
