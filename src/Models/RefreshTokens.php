<?php

namespace App\Models;

use App\Models\Models;
use PDO;

class RefreshTokens extends Models
{
    public function __construct() {}

    //CRUD
    public function saveRefreshToken($data)
    {
        if (empty($data)) {
            return false;
        }

        $fields = [];
        $bindings = [];
        $placeHolders =[];

        foreach ($data as $column => $value) {
            $fields[] = "`$column`";
            $placeHolders =":column";
            $bindings[":column"] = $value;
        }
        $columnString = implode(",", $fields);
        $placeHoldersString = implode("," , $placeHolders);
        $query = "INSERT INTO refresh_tokens ($columnString) VALUES ($placeHolders)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }

    public function getRefreshToken($hashToken)
    {
        $query = "SELECT user_id,token_hash,is_revoked,parent_token_hash,root_token_hash FROM refresh_tokens WHERE token_hash = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$hashToken]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateRefreshToken($data, $hashToken)
    {
        if (empty($data)) {
            return false;
        }

        $fields = [];
        $bindings = [];

        foreach ($data as $column => $value) {
            $fields[] = "`$column` = :$column";
            $bindings[":$column"] = $value;
        }
        $fieldString = implode(",", $fields);
        $sql = "UPDATE refresh_tokens SET $fieldString WHERE `token_hash`=:token_hash";
        $bindings[':token_hash'] =$hashToken;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }
    public function deleteRefreshToken($userId)
    {
        $query = "DELETE FROM refresh_tokens WHERE user_id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->rowCount();
    }
}
