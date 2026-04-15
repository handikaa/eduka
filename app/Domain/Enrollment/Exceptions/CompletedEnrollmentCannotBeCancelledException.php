<?php

namespace App\Domain\Enrollment\Exceptions;

use Exception;

class CompletedEnrollmentCannotBeCancelledException extends Exception
{
    protected $message = 'Enrollment yang sudah selesai tidak dapat dibatalkan.';
}
