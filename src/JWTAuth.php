<?php
namespace JWTAuth;

use JWTAuth\Factories\TokenStorageFactory;
use JWTAuth\Helpers\JWTSlice;
use JWTAuth\Helpers\JWTVerify;
use JWTAuth\Interfaces\TokenStorageInterface;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

trait JWTAuth
{
    protected JWTSlice $JWTSlice;

    protected JWTVerify $JWTVerify;

    protected TokenStorageInterface $tokenStorage;

    private $accessToken;

    private $refreshToken;

    public function __construct()
    {
        $this->tokenStorage = TokenStorageFactory::create(
            config('jwt.token_storage.driver')
        );
    }

    /**
     * Generate access token
     *
     * @param Model $user
     *
     * @return string
     */
    private function generateAccessToken(Model $user): string
    {
        $this->JWTSlice = new JWTSlice();

        $accessHeader = $this->JWTSlice->generateHeader('access');
        $accessPayload =$this->JWTSlice->generatePayload($user, 'access');
        $accessSignature = $this->JWTSlice->generateSignature($accessHeader, $accessPayload);


        $this->accessToken = "$accessHeader.$accessPayload.$accessSignature";

        return $this->accessToken;
    }

    /**
     * Generate refresh token
     *
     * @param Model $user
     *
     * @return string
     */
    private function generateRefreshToken(Model $user): string
    {
        $this->JWTSlice = new JWTSlice();

        $refreshHeader = $this->JWTSlice->generateHeader('refresh');
        $refreshPayload = $this->JWTSlice->generatePayload($user, 'refresh');
        $refreshSignature = $this->JWTSlice->generateSignature($refreshHeader, $refreshPayload);

        $this->refreshToken = "$refreshHeader.$refreshPayload.$refreshSignature";

        return $this->refreshToken;
    }

    /**
     * generate token with custom ttl
     *
     * @param Model $user
     * @param string $tokenType
     * @param ?int $customTtl = null
     *
     * @return string
     */
    public function generateToken(
        Model $user,
        string $tokenType,
        ?int $customTtl = null
    ): string {
        $this->JWTSlice = new JWTSlice();

        if ($customTtl !== null) {
            config(['jwt.ttl' => $customTtl]);
        }

        $header = $this->JWTSlice->generateHeader($tokenType);
        $payload = $this->JWTSlice->generatePayload($user, $tokenType);
        $signature = $this->JWTSlice->generateSignature($header, $payload);

        return "$header.$payload.$signature";
    }

    /**
     * Generate tokens for auth
     *
     * @param Model $user
     *
     * @return array<string | null>
     */
    public function fromUser(Model $user): array {
        $this->generateAccessToken($user);
        $this->generateRefreshToken($user);

        return [ $this->accessToken, $this->refreshToken ];
    }

    /**
     * Verify token
     *
     * @param string $token string
     * @param string $tokenType string<'access' | 'refresh'>
     *
     * @return array<bool|null|Model> [ 'verify_fail' => bool, 'user' => User | null, 'exp' => 9000 ]
     */
    public function verify($token, $tokenType = 'access'): array {
        $this->JWTSlice = new JWTSlice();
        $this->JWTVerify = new JWTVerify();

        $tokenArr = explode(".", $token);
        $data = [
            'verify_fail' => false,
            'user' => null
        ];

        $this->JWTSlice->setHeaderData([
            'totyp' => $tokenType,
            'alg' => config('jwt.algo'),
            'typ' => 'JWT'
        ]);

        $isVerifyHeader = $this->JWTVerify->verifyHeader(
            $tokenArr[0],
            $this->JWTSlice->getHeaderData()['totyp'],
            $this->JWTSlice->getHeaderData()['alg'],
            $this->JWTSlice->getHeaderData()['typ']
        );
        if (!!!$isVerifyHeader) {
            $data['verify_fail'] = true;
            return $data;
        };

        $payloadVerifyResponse = $this->JWTVerify->verifyPayload($tokenArr[1]);
        if (empty($payloadVerifyResponse['user']) || !!!$payloadVerifyResponse['is_verify']) {
            $data['verify_fail'] = true;
            return $data;
        }

        $generateSignature = $this->JWTSlice->generateSignature($tokenArr[0], $tokenArr[1]);
        if ($generateSignature !== $tokenArr[2]) {
            $data['verify_fail'] = true;
            return $data;
        }

        $data['user'] = $payloadVerifyResponse['user'];

        if ($this->tokenStorage->isBlacklisted($token)) {
            $data['verify_fail'] = true;
            $data['user'] = null;
        }

        return [
            ...$data,
            'exp' => $payloadVerifyResponse['exp']
        ];
    }

    /**
     * Recreate tokens
     *
     * @param string $token
     *
     * @return null|array<string|Model|Authenticatable>
     */
    public function refreshToken(string $token): array|null {
        $data = $this->verify($token, 'refresh');

        if ($data['verify_fail'] || empty($data['user'])) return null;


        [ $accessToken, $refreshToken ] = $this->fromUser($data['user']);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'user' => $data['user']
        ];
    }

    /**
     * logout
     *
     * @param string $token
     *
     * @return void
     */
    public function logout(string $token)
    {
        $payload = $this->verify($token);

        if ($payload['user'] && !$payload['verify_fail']) {
            $this->tokenStorage->addToBlacklist(
                $token,
                $payload['exp']
            );
        }
    }

    /**
     * generateInfiniteToken
     *
     * @param Model $user
     * @param string $tokenType
     *
     * @return string
     */
    public function generateInfiniteToken(Model $user, string $tokenType): string
    {
        return $this->generateToken($user, $tokenType, PHP_INT_MAX);
    }
}
