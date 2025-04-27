<?php

namespace JWTAuth\Helpers;

class JWTCoder
{
    /**
     * base64_url_encode
     *
     * @param string $text
     *
     * @return string
     */
    public static function base64_url_encode(string $text): string {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }

    /**
     * base64_url_decode
     *
     * @param string $text
     *
     * @return string
     */
    public static function base64_url_decode(string $text): string {
        return base64_decode(
            str_replace(
                ['-', '_', ''],
                ['+', '/', '='],
                $text
            )
        );
    }
}
