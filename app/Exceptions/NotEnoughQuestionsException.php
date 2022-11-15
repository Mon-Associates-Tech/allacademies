<?php

namespace App\Exceptions;

use Exception;

class NotEnoughQuestionsException extends Exception
{
    public function __construct()
    {
        parent::__construct('Not Enough Questions');
    }
}
