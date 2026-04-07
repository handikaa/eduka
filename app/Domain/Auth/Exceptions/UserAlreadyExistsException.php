<?php

namespace App\Domain\Auth\Exceptions;

use Exception;

class UserAlreadyExistsException extends Exception
{
    protected $message = 'User already exists.';
}
