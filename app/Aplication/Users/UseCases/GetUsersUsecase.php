<?php

namespace App\Aplication\Users\UseCases;

use App\Aplication\Users\DTOs\GetUsersDto;
use App\Domain\Users\Exceptions\UnauthorizedToViewUsersException;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetUsersUsecase
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {}

    public function execute(User $authUser, GetUsersDto $dto): LengthAwarePaginator
    {
        

        return $this->userRepository->getAll(
            perPage: $dto->perPage,
            page: $dto->page,
            role: $dto->role,
            search: $dto->search,
        );
    }
}