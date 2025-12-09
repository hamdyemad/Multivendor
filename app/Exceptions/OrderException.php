<?php

namespace App\Exceptions;

use Exception;

class OrderException extends Exception
{
    protected $errorKey;

    public function __construct(string $key, string $message = null)
    {
        $this->errorKey = $key;
        
        // Use provided message or fall back to translation key
        $finalMessage = $message ?? __($key);
        
        parent::__construct($finalMessage, 422);
    }

    public function getErrorKey()
    {
        return $this->errorKey;
    }
}
