<?php

namespace App\Domain\Lessons\Exceptions;

use Exception;

class LessonNotFoundException extends Exception
{
    protected $message = 'Lesson tidak ditemukan.';
}
