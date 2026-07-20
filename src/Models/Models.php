<?php

namespace App\Models;

use App\Storage\Database;

class Models
{
    protected $db;
    protected $pdo;
    public function __construct()
    {
        $this->db = new Database();
        $this->pdo = $this->db->getPDO();
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}
