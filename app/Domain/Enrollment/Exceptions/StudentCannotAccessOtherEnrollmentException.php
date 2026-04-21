<?php

namespace App\Domain\Enrollment\Exceptions;

use Exception;

class StudentCannotAccessOtherEnrollmentException extends Exception
{
    protected $message = 'Student tidak memiliki akses ke enrollment ini.';
}
