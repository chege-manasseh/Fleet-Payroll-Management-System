<?php

namespace App\Controllers;

use App\Core\Response;

class Controller
{
    protected function json(string $message, mixed $data, int $statusCode = 200)
    {
        $response = new Response($message, $data, $statusCode, ['Content-Type' => 'application/json']);
        return $response->send();
    }

}