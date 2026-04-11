<?php

namespace App\Aplication\Category\UseCases;


use App\Aplication\Category\DTOs\UpdateCategoryDto;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Category\Exceptions\CategoryNotFoundException;
use App\Domain\Category\Exceptions\CategoryAlreadyExistsException;
use App\Aplication\Category\Services\SlugService;

class UpdateCategoryUsecase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private SlugService $slugService
    ) {}

    public function execute(UpdateCategoryDto $dto): array
    {
        $category = $this->categoryRepository->findById($dto->id);

        if (!$category) {
            throw new CategoryNotFoundException();
        }

        // generate slug baru dari name
        $slug = $this->slugService->generate($dto->name);

        // cek apakah slug dipakai category lain
        $existing = $this->categoryRepository->findBySlug($slug);

        if ($existing && $existing->id !== $category->id) {
            throw new CategoryAlreadyExistsException();
        }

        $updatedCategory = $this->categoryRepository->update($category, [
            'name' => $dto->name,
            'slug' => $slug,
        ]);

        return [
            'category' => $updatedCategory
        ];
    }
}
