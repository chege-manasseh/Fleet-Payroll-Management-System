<?php

namespace App\Core;

class Request
{
    private $method;
    private $uri;
    private $data = [];

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];
   
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getData(): array
    {
          // Automatically fetch data depending on the request method
          if ($this->method === 'POST' || $this->method === 'PUT' || $this->method === 'PATCH') {
            // Check if the frontend sent JSON content
            $json = json_decode(file_get_contents('php://input'), true);

            // Fallback to standard application form parameters if JSON is empty
            $this->data = is_array($json) ? $json : $_POST;
        } else {
            $this->data = $_GET;
        }
        return $this->data;
    }

    /**
     * Safely fetch any input value by its key.
     */
    public function input(string $key, $default = null)
    {
        $this->getData();
        $value = $this->data[$key] ?? $default;

        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get all parameters as a flat array.
     */
    public function all(): array
    {
        $this->getData();
        return $this->data;
    }
    public function getHeader(string $key)
    {
        return $_SERVER['HTTP_' . strtoupper($key)] ?? null;
    }
    public function setAttribute(string $key, mixed $value)
    {
        $this->data[$key] = $value;
    }
    public function getAttribute(string $key)
    {
        return $this->data[$key] ?? null;
    }

    public function getCookie(string $key)
    {
        return $_COOKIE[$key] ?? null;
    }
}
