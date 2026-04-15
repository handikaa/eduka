<?php

namespace App\Domain\Courses\Exceptions;

use Exception;

class UnauthorizedCourseAccessException extends Exception
{
    public function __construct()
    {
        parent::__construct('Anda tidak memiliki akses untuk mengubah status course ini.');
    }
}
