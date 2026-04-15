<?php

namespace App\Domain\User\Exceptions;

use Exception;

class OnlyStudentCanEnrollCourseException extends Exception
{
    protected $message = 'Only students can enroll in courses.';
}
