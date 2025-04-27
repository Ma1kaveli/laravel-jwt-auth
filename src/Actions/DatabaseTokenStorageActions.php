<?php

namespace JWTAuth\Actions;

use JWTAuth\Interfaces\TokenStorageInterface;
use JWTAuth\Repositories\DatabaseTokenStorageRepository;
use JWTAuth\Services\DatabaseTokenStorageService;

class DatabaseTokenStorageActions implements TokenStorageInterface {
    public DatabaseTokenStorageService $databaseTokenStorageService;

    public DatabaseTokenStorageRepository $databaseTokenStorageRepository;

    public function __construct() {
        $this->databaseTokenStorageService = new DatabaseTokenStorageService();

        $this->databaseTokenStorageRepository = new DatabaseTokenStorageRepository();
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
        $this->databaseTokenStorageService->create($token, $exp);
    }

    /**
     * isBlacklisted
     *
     * @param string $token
     *
     * @return bool
     */
    public function isBlacklisted(string $token): bool {
        return $this->databaseTokenStorageRepository->isBlacklisted($token);
    }

    /**
     * removeExpired
     *
     * @return int
     */
    public function removeExpired(): int {
        return $this->databaseTokenStorageService->removeExpiredTokens();
    }
}