<?php

namespace JWTAuth\Providers;

use Illuminate\Support\ServiceProvider;
use JWTAuth\Interfaces\TokenStorageInterface;
use JWTAuth\Factories\TokenStorageFactory;

class TokenStorageServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->singleton(TokenStorageInterface::class, function() {
            return TokenStorageFactory::create(
                config('jwt.token_storage.driver')
            );
        });
    }
}