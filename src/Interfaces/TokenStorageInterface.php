<?php

namespace JWTAuth\Interfaces;

interface TokenStorageInterface {
    /**
     * addToBlacklist
     *
     * @param string $token
     * @param int $exp
     *
     * @return void
     */
    public function addToBlacklist(string $token, int $exp): void;

    /**
     * isBlacklisted
     *
     * @param string $token
     *
     * @return bool
     */
    public function isBlacklisted(string $token): bool;

    /**
     * removeExpired
     *
     * @return int
     */
    public function removeExpired(): int;
}