<?php

namespace App\Aplication\Category\DTOs;

class CreateCategoryDto

{
    public function __construct(
        public string $name,
        public string $slug
    ) {}
}
