<?php

namespace App\Controllers\Auth;
use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;

class RefreshToken extends Controller
{
    public $message;
    public $data;

    public function __construct()
    {
        $this->message = 'Refresh token';
        $this->data = null;
    }

    public function refreshToken(Request $request)
    {
        
        $refreshToken = $request->getCookie('refresh_token');
        if(!$refreshToken){
            $this->message = 'Refresh token not found';
            $this->data = null;
            return $this->json($this->message, $this->data, 401);
        }
        $refreshToken = hash($_ENV['JWT_ALGORITHM'], $refreshToken);
        $refreshToken = $this->users->getRefreshToken($refreshToken);
        if(!$refreshToken){
            $this->message = 'Refresh token not found';
            $this->data = null;
            return $this->json($this->message, $this->data, 401);
        }
        $accessToken = JWT::encode($refreshToken, $_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']);
        return $this->json($this->message, $this->data, 200);
    }
}