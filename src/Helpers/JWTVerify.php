<?php
namespace JWTAuth\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JWTVerify
{
    /**
     * Верификация хедера
     *
     * @param string $header
     * @param string $tokenType <success|refresh>
     * @param string $alg
     * @param string $typ
     *
     * @return bool
     */
    public function verifyHeader(
        string $header,
        string $tokenType,
        string $alg,
        string $typ
    ): bool {
        $headerDataRequest = json_decode(JWTCoder::base64_url_decode($header), true);

        if (empty($headerDataRequest) || !!!array_key_exists('alg', $headerDataRequest)
            || !!!array_key_exists('typ', $headerDataRequest)
            || !!!array_key_exists('totyp', $headerDataRequest))
            return false;

        return ($headerDataRequest['alg'] === $alg)
            && ($headerDataRequest['typ'] === $typ)
            && ($headerDataRequest['totyp'] === $tokenType);
    }

    /**
     * Verify payload
     *
     * if return empty user object or isVerify = false it means auth is not unsuccess
     *
     * @param string $payload
     *
     * @return array<bool|Model|null>
     */
    public function verifyPayload($payload) {
        $payloadDataRequest = json_decode(JWTCoder::base64_url_decode($payload), true);

        $badRequest = function () {
            $data['user'] = null;
            $data['is_verify'] = false;

            return $data;
        };

        if (empty($payloadDataRequest) || !!!array_key_exists('sub', $payloadDataRequest)
            || !!!array_key_exists('name', $payloadDataRequest)
            || !!!array_key_exists('exp', $payloadDataRequest)) {
                return $badRequest();
            }

        $subField = config('jwt.sub_payload_field');
        $userModel = config('jwt.user_model');

        $user = $userModel::where($subField, $payloadDataRequest['sub'])->first();

        $getName = function () use ($user) {
            [, $nameValue] = JWTHelpers::getPayloadDataFields($user);

            return $nameValue;
        };

        if (empty($user) || ($payloadDataRequest['name'] !== $getName())) {
            return $badRequest();
        };

        $nowSeconds = strtotime(Carbon::now());

        $data['user'] = $user;
        $data['is_verify'] = true;

        if ($payloadDataRequest['exp'] <= $nowSeconds) {
            $data['is_verify'] = false;
        }

        return [
            ...$data,
            'exp' => $payloadDataRequest['exp']
        ];
    }
}
