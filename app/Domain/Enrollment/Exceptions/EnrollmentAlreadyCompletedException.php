<?php

namespace App\Domain\Enrollment\Exceptions;

use Exception;

class EnrollmentAlreadyCompletedException extends Exception
{
    protected $message = 'Enrollment sudah selesai.';
}
