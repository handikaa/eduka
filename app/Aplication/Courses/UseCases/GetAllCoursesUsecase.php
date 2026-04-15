<?php

namespace App\Aplication\Courses\UseCases;

use App\Aplication\Courses\DTOs\GetAllCoursesDto;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetAllCoursesUsecase
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository
    ) {}

    public function execute(GetAllCoursesDto $dto): LengthAwarePaginator
    {
        return $this->courseRepository->findAll($dto);
    }
}
