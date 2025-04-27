<?php

namespace JWTAuth\Repositories;

use JWTAuth\Models\BlacklistedToken;

use LaravelQueryBuilder\Repositories\BaseRepository;

class DatabaseTokenStorageRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new BlacklistedToken());
    }

    /**
     * isBlacklisted
     *
     * @param string $token
     *
     * @return bool
     */
    public function isBlacklisted(string $token): bool
    {
        return $this->model::where('token', $token)
            ->where('expires_at', '>', time())
            ->exists();
    }
}
