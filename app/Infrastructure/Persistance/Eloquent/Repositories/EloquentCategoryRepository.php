<?php

namespace App\Infrastructure\Persistance\Eloquent\Repositories;

use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\Category;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }

    public function findAll(): array
    {
        return Category::all()->toArray();
    }
}
