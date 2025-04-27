<?php

namespace JWTAuth\Services;

use JWTAuth\Models\BlacklistedToken;

class DatabaseTokenStorageService
{
    /**
     * create
     *
     * @param string $token
     * @param int $exp
     *
     * @return BlacklistedToken
     */
    public function create(string $token, int $exp): BlacklistedToken {
        return BlacklistedToken::create([
            'token' => $token,
            'expires_at' => $exp
        ]);
    }

    /**
     * Delete all expired tokens
     */
    public function removeExpiredTokens(): int {
        return BlacklistedToken::where('expires_at', '<', time())
            ->delete();
    }
}
