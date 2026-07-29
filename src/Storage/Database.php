<?php

namespace App\Storage;

use PDO;

class Database
{
    private $db;
    private $host;
    private $database;
    private $username;
    private $password;
    private $dsn;
    private $pdo;
    public function __construct()
    {
        $this->db = $_ENV['DB_CONNECTION'];
        $this->host = $_ENV['DB_HOST'];
        $this->database = $_ENV['DB_DATABASE'];
        $this->username = $_ENV['DB_USERNAME'];
        $this->password = $_ENV['DB_PASSWORD'];
        $this->dsn = $this->db . ':host=' . $this->host . ';dbname=' . $this->database;
        $this->pdo = new PDO($this->dsn, $this->username, $this->password);
    }


    public function getPDO()
    {
        return $this->pdo;
    }
}
