<?php

namespace App\Domain\Enrollment\Exceptions;

use Exception;

class UnauthorizedToViewEnrollmentDetailException extends Exception
{
    protected $message = 'Anda tidak memiliki akses untuk melihat detail enrollment ini.';
}
