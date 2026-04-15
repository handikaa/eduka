<?php

namespace App\Aplication\Category\UseCases;

use App\Aplication\Category\DTOs\DeleteCategoryDto;
use App\Domain\Category\Exceptions\CategoryNotFoundException;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;

class DeleteCategoryUsecase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function execute(DeleteCategoryDto $dto): void
    {
        $category = $this->categoryRepository->findById($dto->id);

        if (!$category) {
            throw new CategoryNotFoundException();
        }

        $this->categoryRepository->delete($category);
    }
}
