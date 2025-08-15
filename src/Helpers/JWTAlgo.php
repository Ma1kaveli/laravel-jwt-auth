<?php

namespace JWTAuth\Helpers;

use Exception;

class JWTAlgo
{
    public static function getOpenSSLAlgo(string $algo): int {
        $mapping = [
            'RS256' => OPENSSL_ALGO_SHA256,
            'RS384' => OPENSSL_ALGO_SHA384,
            'RS512' => OPENSSL_ALGO_SHA512,
            'ES256' => OPENSSL_ALGO_SHA256,
            'ES384' => OPENSSL_ALGO_SHA384,
            'ES512' => OPENSSL_ALGO_SHA512,
            'EC256' => OPENSSL_ALGO_SHA256,
            'EC384' => OPENSSL_ALGO_SHA384,
            'EC512' => OPENSSL_ALGO_SHA512,
        ];

        return $mapping[$algo] ?? throw new Exception("Unsupported algorithm: $algo", 400);
    }

    public static function getECDSAComponentLength(string $algo): int {
        if (strpos($algo, '256') !== false) return 32;
        if (strpos($algo, '384') !== false) return 48;
        if (strpos($algo, '512') !== false) return 66;
        throw new Exception("Unsupported ECDSA algorithm: $algo", 400);
    }

    public static function convertECSignature(string $derSignature, int $componentLength): string {
        $offset = 0;

        if ($derSignature[$offset++] !== "\x30") {
            throw new Exception("Invalid DER signature", 400);
        }

        $len = ord($derSignature[$offset++]);
        if ($len + 2 !== strlen($derSignature)) {
            throw new Exception("Invalid DER length", 400);
        }

        // Read R
        if ($derSignature[$offset++] !== "\x02") {
            throw new Exception("Expected INTEGER for R", 400);
        }

        $rLen = ord($derSignature[$offset++]);
        $r = substr($derSignature, $offset, $rLen);
        $offset += $rLen;

        // Remove leading zero if needed
        if ($rLen > $componentLength && $r[0] === "\x00") {
            $r = substr($r, 1);
        }

        // Read S
        if ($derSignature[$offset++] !== "\x02") {
            throw new Exception("Expected INTEGER for S", 400);
        }

        $sLen = ord($derSignature[$offset]);
        $s = substr($derSignature, $offset + 1, $sLen);

        // Remove leading zero if needed
        if ($sLen > $componentLength && $s[0] === "\x00") {
            $s = substr($s, 1);
        }

        return str_pad($r, $componentLength, "\0", STR_PAD_LEFT)
            . str_pad($s, $componentLength, "\0", STR_PAD_LEFT);
    }
}
