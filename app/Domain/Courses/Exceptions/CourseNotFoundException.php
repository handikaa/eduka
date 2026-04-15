<?php

namespace App\Domain\Courses\Exceptions;

use Exception;

class CourseNotFoundException extends Exception
{
    protected $message = 'Course not found';
}
