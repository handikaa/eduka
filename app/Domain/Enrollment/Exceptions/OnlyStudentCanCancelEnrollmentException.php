<?php

namespace App\Domain\Enrollment\Exceptions;

use Exception;

class OnlyStudentCanCancelEnrollmentException extends Exception
{
    protected $message = 'Hanya student yang dapat membatalkan enrollment.';
}
