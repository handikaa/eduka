<?php

namespace App\Domain\Enrollment\Exceptions;

use Exception;

class EnrollmentNotFoundException extends Exception
{
    protected $message = 'Enrollment tidak ditemukan.';
}
