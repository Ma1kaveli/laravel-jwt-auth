<?php

namespace JWTAuth\Providers;

use JWTAuth\Console\Commands\JWTSecretGenerate;
use JWTAuth\Console\Commands\CMigrateLogger;

use Illuminate\Support\ServiceProvider;

class JWTAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register config
        $this->mergeConfigFrom(
            __DIR__.'/../../config/jwt.php',
            'jwt'
        );
    }

    public function boot()
    {
        // Register migration without publication
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');

        // Publisg config
        $this->publishes([
            __DIR__.'/../../config/jwt.php' => config_path('jwt.php'),
        ], 'jwt-config');

        // Registret console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                JWTSecretGenerate::class,
                CMigrateLogger::class
            ]);
        }
    }
}
