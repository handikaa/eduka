<?php

namespace App\Domain\Enrollment\Exceptions;

use Exception;

class OnlyInstructorCanAccessCourseEnrollmentsException extends Exception
{
    protected $message = 'Hanya instructor yang dapat melihat daftar enrollment course.';
}
