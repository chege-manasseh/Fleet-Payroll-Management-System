<?php

namespace App\Models;



class ResetPasswordTokens extends Models
{
    public function createResetToken(array $data)
    {
        $fields = [];
        $bindings = [];
        $placeHolders =[];

        foreach ($data as $column => $value) {
            $fields[]="`$column`";
            $placeHolders[]=":$column";
            $bindings[":$column"]= $value;
        }
        $columnString = implode(",",$fields);
        $placeHolderString = implode(",",$placeHolders);
        $query = "INSERT INTO password_reset_tokens ($columnString)VALUES ($placeHolderString)";
        $stmt = $this->pdo->prepare($query);
        
        return $stmt->execute($bindings);
    }
}
