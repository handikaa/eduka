<?php

namespace App\Domain\CourseReview\Exceptions;

use Exception;

class InvalidCourseReviewRatingException extends Exception
{
    protected $message = 'Rating review harus bernilai antara 1 sampai 5.';
}
