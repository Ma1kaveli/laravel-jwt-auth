<?php

namespace JWTAuth\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JWTSlice
{
    /**
     * HEADER_DATA
     *
     * @var array
     */
    private $HEADER_DATA = [];

    /**
     * PAYLOAD_DATA
     *
     * @var array
     */
    private $PAYLOAD_DATA = [];

    /**
     * setHeaderData
     *
     * @param array $data
     *
     * @return void
     */
    public function setHeaderData(array $data): void
    {
        $this->HEADER_DATA = $data;
    }

    /**
     * setPayloadData
     *
     * @param array $data
     *
     * @return void
     */
    public function setPayloadData(array $data): void
    {
        $this->PAYLOAD_DATA = $data;
    }

    /**
     * getHeaderData
     *
     * @return array
     */
    public function getHeaderData(): array
    {
        return $this->HEADER_DATA;
    }

    /**
     * getPayloadData
     *
     * @return array
     */
    public function getPayloadData(): array
    {
        return $this->PAYLOAD_DATA;
    }

    /**
     * Header to base64
     *
     * @param string $tokenType
     *
     * @return string
     *
     */
    public function generateHeader(string $tokenType): string {
        $this->HEADER_DATA['totyp'] = $tokenType;
        $this->HEADER_DATA['alg'] = config('jwt.algo');
        $this->HEADER_DATA['typ'] = 'JWT';

        $headerDataStr = json_encode($this->HEADER_DATA);

        return JWTCoder::base64_url_encode($headerDataStr);
    }

    /**
     * generate payload
     *
     * @param Model $user
     *
     * @return string
     */
    public function generatePayload(Model $user, string $tokenType): string {
        $ttl = $tokenType === 'access'
            ? config('jwt.ttl')
            : config('jwt.refresh_ttl');

        $nowDate = Carbon::now();

        [$subValue, $nameValue] = JWTHelpers::getPayloadDataFields($user);

        $this->PAYLOAD_DATA['sub'] = $subValue;
        $this->PAYLOAD_DATA['name'] = $nameValue;
        $this->PAYLOAD_DATA['exp'] = config('jwt.allow_infinite_ttl', false)
            ? config('infinite_ttl_fallback', 31536000)
            : strtotime($nowDate) + $ttl * 60;

        $payloadDataStr = json_encode($this->PAYLOAD_DATA);

        return JWTCoder::base64_url_encode($payloadDataStr);
    }

    /**
     * Generate token signature
     *
     * @return string
     */
    public function generateSignature($header, $payload): string {
        $secretKey = config('jwt.private');
        $string = "$header.$payload";

        return JWTCoder::base64_url_encode(
            hash_hmac(
                config('jwt.algo'),
                $string,
                $secretKey,
                true
            )
        );
    }
}
