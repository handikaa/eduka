<?php

namespace App\Domain\Enrollment\Exceptions;

use Exception;

class CancelledEnrollmentCannotBeCompletedException extends Exception
{
    protected $message = 'Enrollment yang dibatalkan tidak dapat diselesaikan.';
}
