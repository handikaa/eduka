<?php

namespace App\Domain\User\Exceptions;

use Exception;

class OnlyStudentCanAccessEnrollmentException extends Exception
{
    protected $message = 'Only students can access in courses.';
}
