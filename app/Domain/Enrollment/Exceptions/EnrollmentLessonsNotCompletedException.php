<?php

namespace App\Domain\Enrollment\Exceptions;

use Exception;

class EnrollmentLessonsNotCompletedException extends Exception
{
    protected $message = 'Semua lesson pada course belum selesai dipelajari.';
}
