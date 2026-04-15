<?php

namespace App\Domain\Enrollment\Exceptions;

use Exception;

class OnlyStudentCanCompleteEnrollmentException extends Exception
{
    protected $message = 'Hanya student yang dapat menyelesaikan enrollment.';
}
