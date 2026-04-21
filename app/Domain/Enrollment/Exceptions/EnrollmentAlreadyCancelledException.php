<?php

namespace App\Domain\Enrollment\Exceptions;

use Exception;

class EnrollmentAlreadyCancelledException extends Exception
{
    protected $message = 'Enrollment sudah dibatalkan.';
}
