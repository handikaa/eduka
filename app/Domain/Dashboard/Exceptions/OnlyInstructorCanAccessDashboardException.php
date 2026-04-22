<?php

namespace App\Domain\Dashboard\Exceptions;

use Exception;

class OnlyInstructorCanAccessDashboardException extends Exception
{
    protected $message = 'Hanya instructor yang dapat mengakses dashboard instructor.';
}
