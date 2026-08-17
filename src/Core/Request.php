<?php

namespace App\Core;

class Request
{
    private $method;
    private $uri;
    private array $body = [];        // request payload only
    private array $attributes = [];  // middleware / internal context
    private bool $bodyLoaded = false;

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
        if (!$this->bodyLoaded) {
            if (in_array($this->method, ['POST', 'PUT', 'PATCH'], true)) {
                $json = json_decode(file_get_contents('php://input'), true);
                $this->body = is_array($json) ? $json : $_POST;
            } else {
                $this->body = $_GET;
            }
            $this->bodyLoaded = true;
        }
        return $this->body;
    }

    /**
     * Safely fetch any input value by its key.
     */
    public function input(string $key, $default = null)
    {
        $value = $this->getData()[$key] ?? $default;
        // Only escape when outputting to HTML — NOT for passwords
        return is_string($value)
            ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
            : $value;
    }
    /**
     * Get all parameters as a flat array.
     */
    // Raw value — use for passwords
    public function raw(string $key, $default = null)
    {
        return $this->getData()[$key] ?? $default;
    }
    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }
    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }
    public function getHeader(string $key)
    {
        return $_SERVER['HTTP_' . strtoupper($key)] ?? null;
    }


    public function getCookie(string $key)
    {
        return $_COOKIE[$key] ?? null;
    }
}
