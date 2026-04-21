<?php

namespace App\Domain\User\Exceptions;

use Exception;

class StudentAlreadyEnrollException extends Exception
{
    protected $message = 'Student already enrolled in this course.';
}
