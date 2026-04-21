<?php

namespace App\Domain\Enrollment\Exceptions;

use Exception;

class CourseQuotaIsFullException extends Exception
{
    protected $message = 'Course quota is full.';
}
