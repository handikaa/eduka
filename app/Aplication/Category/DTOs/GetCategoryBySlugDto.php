<?php

namespace App\Aplication\Category\DTOs;

class GetCategoryBySlugDto
{
    public function __construct(
        public string $slug
    ) {}
}
