<?php

namespace App\Domain\Auth\Exceptions;

use Exception;

class InactiveUserException extends Exception
{
    protected $message = 'User is inactive.';
}
