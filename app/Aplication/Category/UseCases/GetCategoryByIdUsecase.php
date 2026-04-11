<?php

namespace App\Aplication\Category\UseCases;

use App\Aplication\Category\DTOs\GetCategoryByIdDto;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Category\Exceptions\CategoryNotFoundException;

class GetCategoryByIdUsecase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(GetCategoryByIdDto $dto): array
    {
        $category = $this->categoryRepository->findById($dto->id);

        if (!$category) {
            throw new CategoryNotFoundException();
        }

        return [
            'category' => $category
        ];
    }
}
