<?php

namespace JWTAuth\Services;

use JWTAuth\Constants\RedisConstants;

use Illuminate\Support\Facades\Redis;

class RedisTokenStorageService {
    /**
     * add
     *
     * @param string $token
     * @param int $exp
     * @param int $storageTtl
     *
     * @return void
     */
    public function add(string $token, int $exp, int $storageTtl): void {
        $key = RedisConstants::PREFIX . hash('sha256', $token);
        Redis::setex($key, $storageTtl, $exp);
    }
}
