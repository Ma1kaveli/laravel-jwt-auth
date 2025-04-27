<?php

namespace JWTAuth\Console\Commands;

use JWTAuth\Interfaces\TokenStorageInterface;

use Illuminate\Console\Command;

class JWTClearExpiredTokensCommand extends Command
{
    /**
     * signature
     *
     * @var string
     */
    protected $signature = 'jwt:clear-tokens';

    /**
     * description
     *
     * @var string
     */
    protected $description = 'Remove expired tokens from storage';


    /**
     * handle
     *
     * @param TokenStorageInterface $storage
     *
     * @return void
     */
    public function handle(TokenStorageInterface $storage)
    {
        $count = $storage->removeExpired();
        $this->info("Cleared $count expired tokens");
    }
}