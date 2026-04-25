<?php

namespace App\Domain\Users\Exceptions;

use Exception;

class UnauthorizedToViewUsersException extends Exception
{
    protected $message = 'Anda tidak memiliki akses untuk melihat daftar user.';
}