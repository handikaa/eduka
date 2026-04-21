<?php

namespace App\Domain\User\Exceptions;

use Exception;

class OnlyStudentCanCompleteLessonProgressException extends Exception
{
    protected $message = 'Hanya student yang dapat menyelesaikan progress lesson.';
}
