<?php

namespace App\Domain\Courses\Exceptions;

use Exception;

class CategoryIsRequiredException extends Exception
{
    protected $message = 'Category is required';
}
