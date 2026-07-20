<?php

namespace App\Core;

class Response
{
    private string $message;
    private mixed $data;
    private int $statusCode;
    private array $headers;

    public function __construct(string $message, mixed $data = null, int $statusCode=200, array $headers = [])
    {
        $this->message = $message;
        $this->data = $data;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Builds the JSON envelope. Does not output anything.
     */
    public function getBody(): string
    {
            $envelope = [
            'success' => $this->statusCode >= 200 && $this->statusCode < 300 ? true : false,
            'message' => $this->message,
            'data' => $this->data,
        ];
    
        return json_encode($envelope, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Sends headers and echoes the JSON payload to the client.
     */
    public function send(): string
    {
        http_response_code($this->statusCode);

        header('Content-Type: application/json; charset=utf-8');

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        $payload = $this->getBody();
        echo $payload;

        return $payload;
    }
}
