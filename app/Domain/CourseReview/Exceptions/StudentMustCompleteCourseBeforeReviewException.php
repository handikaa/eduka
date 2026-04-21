<?php

namespace App\Domain\CourseReview\Exceptions;

use Exception;

class StudentMustCompleteCourseBeforeReviewException extends Exception
{
    protected $message = 'Student harus menyelesaikan course sebelum dapat memberikan review.';
}
