<?php

namespace App\Controllers;

use App\Core\Response;

class Controller
{
    private Response $response;
    public function __construct(Response $response)
    {
        $this->response=$response;
        throw new \Exception('Not implemented');
    }
    protected function json(string $message, mixed $data, int $statusCode = 200)
    {
        return $this->response->json($message,$data,$statusCode);
    }

}