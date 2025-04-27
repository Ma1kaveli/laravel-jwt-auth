<?php

namespace JWTAuth\Factories;

use JWTAuth\Actions\DatabaseTokenStorageActions;
use JWTAuth\Actions\RedisTokenStorageActions;
use JWTAuth\Interfaces\TokenStorageInterface;

use InvalidArgumentException;

class TokenStorageFactory {
    public static function create(string $driver): TokenStorageInterface {
        return match ($driver) {
            'database' => new DatabaseTokenStorageActions(),
            'redis' => new RedisTokenStorageActions(),
            default => throw new InvalidArgumentException("Unsupported storage driver: $driver"),
        };
    }
}