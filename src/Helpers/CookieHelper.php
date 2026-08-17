<?php

namespace App\Helpers;


class CookieHelper
{


    public function setCookies($accessToken, $refreshToken)
    {
        setcookie('access_token', $accessToken, [
            'expires' => time() + $_ENV['JWT_EXPIRATION'],
            'path' => '/api',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        setcookie('refresh_token', $refreshToken, [
            'expires' => time() + (30 * 24 * 60 * 60),
            'path' => '/api/refresh',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    public function clearCookies()
    {

        setcookie('refresh_token', '', time() - 3600, '/api/refresh');
        setcookie('access_token', '', time() - 3600, '/api');
    }
}
