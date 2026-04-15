<?php

namespace App\Domain\Courses\Exceptions;

use Exception;

class InvalidCourseStatusException extends Exception
{
    public function __construct(array $allowedStatuses = [])
    {
        $message = 'Status course tidak valid.';

        if (!empty($allowedStatuses)) {
            $message .= ' Status yang diperbolehkan: ' . implode(', ', $allowedStatuses);
        }

        parent::__construct($message);
    }
}
