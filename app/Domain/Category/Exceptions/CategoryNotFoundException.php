<?php

namespace App\Domain\Category\Exceptions;

use Exception;

class CategoryNotFoundException extends Exception
{
    protected $message = 'Category not found';
}
