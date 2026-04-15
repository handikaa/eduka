<?php

namespace App\Aplication\Category\UseCases;

use App\Aplication\Category\DTOs\CreateCategoryDto;
use App\Aplication\Category\Services\SlugService;
use App\Domain\Category\Exceptions\CategoryAlreadyExistsException;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;

class CreateCategoryUsecase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private SlugService $slugService
    ) {}

    public function execute(CreateCategoryDto $dto): array
    {
        $slug = $this->slugService->generate($dto->name);

        $existing = $this->categoryRepository->findBySlug($slug);

        if ($existing) {
            throw new CategoryAlreadyExistsException();
        }

        $category = $this->categoryRepository->create([
            'name' => $dto->name,
            'slug' => $slug,
        ]);

        return [
            'category' => $category
        ];
    }
}
