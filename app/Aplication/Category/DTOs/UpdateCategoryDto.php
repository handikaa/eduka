<?php


namespace App\Aplication\Category\DTOs;

class UpdateCategoryDto
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
