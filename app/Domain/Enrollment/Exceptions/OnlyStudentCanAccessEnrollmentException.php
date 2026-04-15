<?php

namespace App\Domain\Enrollment\Exceptions;

use Exception;

class OnlyStudentCanAccessEnrollmentException extends Exception
{
    protected $message = 'Hanya student yang dapat mengakses course melalui enrollment.';
}
