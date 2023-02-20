<?php

namespace App\Exceptions;

use Exception;

class RouteNotFoundException extends Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message);
    }
}
