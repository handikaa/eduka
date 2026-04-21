<?php

namespace App\Domain\CourseReview\Exceptions;

use Exception;

class DeletedCourseReviewNotFoundException extends Exception
{
    protected $message = 'Review course yang dihapus tidak ditemukan.';
}
