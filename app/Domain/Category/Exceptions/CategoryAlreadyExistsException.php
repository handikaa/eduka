<?php

namespace App\Domain\Category\Exceptions;

use Exception;

class CategoryAlreadyExistsException extends Exception
{
    protected $message = 'Category already exists';
}
