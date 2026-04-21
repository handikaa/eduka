<?php

namespace App\Domain\CourseReview\Exceptions;

use Exception;

class StudentAlreadyReviewedCourseException extends Exception
{
    protected $message = 'Student sudah memberikan review untuk course ini.';
}
