<?php

namespace App\Domain\CourseReview\Exceptions;

use Exception;

class CourseReviewNotFoundException extends Exception
{
    protected $message = 'Review course tidak ditemukan.';
}
