<?php

namespace App\Authorization;

use Exception;

class ForbiddenException extends Exception
{
    public function __construct(string $message = 'Forbidden', int $code = 403)
    {
        parent::__construct($message, $code);
    }
}
