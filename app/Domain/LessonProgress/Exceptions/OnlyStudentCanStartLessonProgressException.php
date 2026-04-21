<?php

namespace App\Domain\LessonProgress\Exceptions;

use Exception;

class OnlyStudentCanStartLessonProgressException extends Exception
{
    protected $message = 'Hanya student yang dapat memulai progress lesson.';
}
