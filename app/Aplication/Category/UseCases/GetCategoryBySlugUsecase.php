<?php

namespace App\Aplication\Category\UseCases;

use App\Aplication\Category\DTOs\GetCategoryBySlugDto;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Category\Exceptions\CategoryNotFoundException;

class GetCategoryBySlugUsecase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(GetCategoryBySlugDto $dto): array
    {
        $category = $this->categoryRepository->findBySlug($dto->slug);

        if (!$category) {
            throw new CategoryNotFoundException();
        }

        return [
            'category' => $category
        ];
    }
}
