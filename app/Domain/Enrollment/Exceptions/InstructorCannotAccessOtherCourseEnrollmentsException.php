<?php

namespace App\Domain\Enrollment\Exceptions;

use Exception;

class InstructorCannotAccessOtherCourseEnrollmentsException extends Exception
{
    protected $message = 'Anda tidak memiliki akses ke daftar enrollment course ini.';
}
