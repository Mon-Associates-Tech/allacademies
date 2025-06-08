<?php

namespace App\Exceptions;

use Exception;

class NoTopicsException extends Exception
{
    public function __construct()
    {
        parent::__construct('No topics available.');
    }
}
