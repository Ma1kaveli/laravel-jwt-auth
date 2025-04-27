<?php

namespace JWTAuth\Actions;

use JWTAuth\Interfaces\TokenStorageInterface;
use JWTAuth\Repositories\RedisTokenStorageRepository;
use JWTAuth\Services\RedisTokenStorageService;

class RedisTokenStorageActions implements TokenStorageInterface {
    private RedisTokenStorageService $service;

    private RedisTokenStorageRepository $repository;

    private int $storageTtl;

    public function __construct() {
        $this->service = new RedisTokenStorageService();

        $this->repository = new RedisTokenStorageRepository();

        $this->storageTtl = config('jwt.token_storage.storage_ttl');
    }

    /**
     * addToBlacklist
     *
     * @param string $token
     * @param int $exp
     *
     * @return void
     */
    public function addToBlacklist(string $token, int $exp): void {
        $this->service->add($token, $exp, $this->storageTtl);
    }

    /**
     * isBlacklisted
     *
     * @param string $token
     *
     * @return bool
     */
    public function isBlacklisted(string $token): bool {
        return $this->repository->isBlacklisted($token, $this->storageTtl);
    }

    /**
     * removeExpired
     *
     * delete in redis occurs by ttl
     *
     * @return int
     */
    public function removeExpired(): int {
        return 0;
    }
}