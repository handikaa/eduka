<?php

namespace App\Aplication\Auth\UseCases;

use App\Aplication\Auth\DTOs\GetUserByIdDto;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Exceptions\UserNotFoundException;

class GetUserByIdUsecase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function execute(GetUserByIdDto $dto): array
    {
        $user = $this->userRepository->findById($dto->id);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return [
            'user' => $user,
        ];
    }
}
