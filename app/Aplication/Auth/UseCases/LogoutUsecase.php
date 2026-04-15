<?php

namespace App\Aplication\Auth\UseCases;

use App\Aplication\Auth\DTOs\LogoutUserDto;
use App\Domain\User\Repositories\UserRepositoryInterface;


class LogoutUsecase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,

    ) {}

    public function execute(LogoutUserDto $dto): void
    {
        // Implement logout logic here, e.g., invalidate the user's token
        $this->userRepository->logout($dto->user);
    }
}
