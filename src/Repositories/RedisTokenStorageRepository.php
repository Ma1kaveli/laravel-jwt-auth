<?php

namespace JWTAuth\Repositories;

use JWTAuth\Constants\RedisConstants;

use Illuminate\Support\Facades\Redis;

class RedisTokenStorageRepository
{
    /**
     * getExpiration
     *
     * @param string $token
     *
     * @return int
     */
    public function getExpiration(string $token): ?int {
        $key = RedisConstants::PREFIX . hash('sha256', $token);
        $exp = Redis::get($key);

        return $exp ? (int)$exp : null;
    }

    /**
     * isBlacklisted
     *
     * @param string $token
     * @param int $exp
     *
     * @return bool
     */
    public function isBlacklisted(string $token, int $exp): bool {
        $isExistToken = (bool) Redis::exists(RedisConstants::PREFIX . hash('sha256', $token));
        if (!$isExistToken) {
            return false;
        }

        $storedExp = $this->getExpiration($token);
        return $storedExp >= time();
    }
}