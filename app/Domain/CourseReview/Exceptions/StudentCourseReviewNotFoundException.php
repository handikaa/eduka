<?php

namespace App\Domain\CourseReview\Exceptions;

use Exception;

class StudentCourseReviewNotFoundException extends Exception
{
    public function __construct(
        string $message = 'Review student pada course tidak ditemukan'
    ) {
        parent::__construct($message);
    }
}
