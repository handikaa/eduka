<?php

namespace App\Domain\Dashboard\Exceptions;

use Exception;

class OnlyStudentCanAccessDashboardException extends Exception
{
    protected $message = 'Hanya student yang dapat mengakses dashboard student.';
}
