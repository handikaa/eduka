<?php

namespace App\Domain\Category\Repositories;

use App\Infrastructure\Persistance\Eloquent\Models\Category;


interface CategoryRepositoryInterface
{
    public function create(array $data): Category;

    public function findById(int $id): ?Category;
    public function findBySlug(string $slug): ?Category;

    public function findAll(): array;

    public function update(Category $category, array $data): Category;

    public function delete(Category $category): void;
}
