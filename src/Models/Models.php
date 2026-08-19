<?php

namespace App\Models;

use App\Storage\Database;

class Models
{
    protected $db;
    protected $pdo;
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->pdo = $db->getPDO();
    }
}
