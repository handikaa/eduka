<?php

namespace App\Domain\CourseReview\Exceptions;

use Exception;

class OnlyStudentCanCreateCourseReviewException extends Exception
{
    protected $message = 'Hanya student yang dapat membuat review course.';
}
