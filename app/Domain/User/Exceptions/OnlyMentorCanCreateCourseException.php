<?php

namespace App\Domain\User\Exceptions;

use Exception;

class OnlyMentorCanCreateCourseException extends Exception
{
    protected $message = 'Only mentors can create courses.';
}
