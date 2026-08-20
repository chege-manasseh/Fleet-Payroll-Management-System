<?php

namespace App\Authorization;


interface Authorization
{
    public function can(
        int $userId,
        string $permission
    ): bool;

    public function assertCan(
        int $userId,
        string $permission
    ): void;
}
