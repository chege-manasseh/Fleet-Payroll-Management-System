<?php

namespace App\Controllers\Middleware;

use App\Core\Request;
use App\Core\Response;
use Exception;
use App\Controllers\Controller;
use App\Storage\Database;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware extends Controller
{
    public $message;
    public $data;
    public $request;
    public $response;
    public $db;

    public function __construct(Request $request,Response $response)
    {
        $this->request = $request;
        $this->response = $response;
      
    }
    
    public function handle()
    {
        try{
            $token = $this->request->getCookie('access_token');
            if(!$token){
                $message = 'Unauthorized';
                $data = ['error' => 'INVALID_TOKEN'];
                return $this->response->json($message, $data, 401);
            }

            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']));
            $this->request->setAttribute('user', $decoded);
            return true;
        }catch(Exception $e){
            $message = 'Unauthorized';
            $data = ['error' => 'Unauthorized'];
            return $this->response->json($message, $data, 401);
        }
    }   
}