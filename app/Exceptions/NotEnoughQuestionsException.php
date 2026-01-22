<?php

namespace App\Exceptions;

use Exception;

class NotEnoughQuestionsException extends Exception
{
    public string $msg = 'Not enough questions';

    public function __construct(?string $message)
    {
        $this->msg = $message;
        parent::__construct($this->msg);
    }
}
