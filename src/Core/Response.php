<?php

namespace App\Core;
use App\Core\Request;

class Response
{

    public function __construct() {
    }

    /**
     * Builds the JSON envelope. Does not output anything.
     */

    public function json(string $message, mixed $data = null, int $statusCode = 200)
    {
        $statusCode = $statusCode ?? 200;
        $message = $message ?? 'Success';
        $data = $data;
        $headers = $headers ?? [];

        $payload = $this->getBody($message, $data, $statusCode, $headers);
        return $this->send($payload, $statusCode, $headers);
    }
    public function getBody(string $message, mixed $data = null, int $statusCode = 200, array $headers = []): string
    {
        $envelope = [
            'success' => $statusCode >= 200 && $statusCode < 300 ? true : false,
            'message' => $message,
            'data' => $data,
            'headers' => $headers,
        ];

        return json_encode($envelope, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }


    /**
     * Sends headers and echoes the JSON payload to the client.
     */
    public function send(string $payload, int $statusCode = 200, array $headers = []): string
    {
        http_response_code($statusCode);

        header('Content-Type: application/json; charset=utf-8');
        
        foreach ($headers as $key => $value) {
            header("$key: $value");
        }

        echo $payload;

        return $payload;
    }
}
