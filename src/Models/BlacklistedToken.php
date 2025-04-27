<?php

namespace JWTAuth\Models;

use Illuminate\Database\Eloquent\Model;

class BlacklistedToken extends Model
{
    /**
     * table
     *
     * @var string
     */
    protected $table = 'blacklisted_tokens';

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'token',
        'expires_at'
    ];

    /**
     * Disabled primary key
     */
    public $incrementing = false;

    /**
     * Primary key - string
     */
    protected $keyType = 'string';

    /**
     * Primary key поле
     */
    protected $primaryKey = 'token';

    /**
     * Check token actual
     */
    public function isExpired(): bool
    {
        return $this->expires_at < time();
    }
}