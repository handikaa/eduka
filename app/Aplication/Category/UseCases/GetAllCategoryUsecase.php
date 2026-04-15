<?php

namespace App\Aplication\Category\UseCases;;

use App\Aplication\Category\Services\SlugService;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;

class GetAllCategoryUsecase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,

    ) {}

    public function execute(): array
    {
        $categories = $this->categoryRepository->findAll();

        return [
            'categories' => $categories
        ];
    }
}
