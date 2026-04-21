<?php

namespace App\Domain\LessonProgress\Exceptions;

use Exception;

class OnlyStudentCanUpdateLastAccessedLessonProgressException extends Exception
{
    protected $message = 'Hanya student yang dapat memperbarui last accessed lesson progress.';
}
