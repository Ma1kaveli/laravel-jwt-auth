<?php

namespace JWTAuth\Middleware;

use JWTAuth\JWTAuth;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelLogger\Facades\LaravelLog;

class CheckAuthenticate
{
    use JWTAuth;

    /**
     * getTestUserData
     *
     * @param mixed $token
     *
     * @return array
     */
    public function getTestUserData($token): array|null
    {
        $testToken = config('jwt.test_token');
        $getTestUser = config('jwt.get_test_auth_user_func');

        if (!empty($testToken) && ($testToken === $token) && !empty($getTestUser)) {

            $user = $getTestUser();

            $data = [
                'user' => $user,
                'verify_fail' => false,
            ];

            return $data;
        }

        return null;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $headers = $request->header();

        if(!array_key_exists('authorization', $headers)){
            return response(['message' => 'Не авторизован!'], 401);
        }

        try {
            $token = explode(' ', $headers['authorization'][0])[1];
            $data = $this->getTestUserData($token);
            if (empty($data)) {
                $data = $this->verify($token);
            }
        } catch (Exception $e) {
            LaravelLog::error($e);
            return response(['message' => 'Не авторизован!'], 401);
        }

        if ($data['verify_fail'] || empty($data['user'])) return response(['message' => 'Не авторизован!'], 401);
        Auth::setUser($data['user']);

        return $next($request);
    }
}
