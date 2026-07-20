<?php

namespace App\Controllers\Middleware;

use App\Core\Request;
use App\Core\Response;
use Exception;
use App\Controllers\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware extends Controller
{
    public $message;
    public $data;
    
    public function handle(Request $request, Response $response)
    {
        try{
            $token = $request->getHeader('Authorization');
            if(!$token){
                $message = $this->message = 'Unauthorized';
                $data = $this->data = ['error' => 'Unauthorized'];
                return $this->json($message, $data, 401);
            }
            $token = str_replace('Bearer ', '', $token);
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']));
            $request->setAttribute('user', $decoded);
            return true;
        }catch(Exception $e){
            $message = $this->message = 'Unauthorized';
            $data = $this->data = ['error' => $e->getMessage()];
            return $this->json($message, $data, 401);
        }
    }   
}