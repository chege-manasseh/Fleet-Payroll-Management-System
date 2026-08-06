<?php

namespace App\Services\Auth;
use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Users;
use App\Models\RefreshTokens;

class RefreshToken
{
    public $message;
    public $data;
    private Response $response;
    private RefreshTokens $refreshTokens;

    public function __construct( Response $response,RefreshTokens $refreshTokens)
    {
        $this->message = 'Refresh token';
        $this->data = null;
        $this->response= $response;
        $this->refreshTokens=$refreshTokens;
    }

    public function refreshToken(Request $request)
    {
        
        $refreshToken = $request->getCookie('refresh_token');
        if(!$refreshToken){
            $this->message = 'Refresh Token Invalid';
            $this->data = null;
            return $this->response->json($this->message, $this->data, 401);
        }
        $refreshToken = hash($_ENV['JWT_ALGORITHM'], $refreshToken);
        $refreshToken = $this->refreshTokens->getRefreshToken($refreshToken);
        if(!$refreshToken){
            $this->message = 'Refresh token not found';
            $this->data = null;
            return $this->response->json($this->message, $this->data, 401);
        }
        $accessToken = JWT::encode($refreshToken, $_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']);
        return $this->response->json($this->message, $this->data, 200);
    }
}