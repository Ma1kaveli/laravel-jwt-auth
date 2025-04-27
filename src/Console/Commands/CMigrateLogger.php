<?php

namespace JWTAuth\Console\Commands;

use Illuminate\Console\Command;

class CMigrateLogger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:jwt-auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run package migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('migrate', [
            '--path' => 'vendor/makaveli/laravel-jwt-auth/src/Database/migrations',
            '--force' => true
        ]);
    }
}