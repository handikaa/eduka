<?php

namespace App\Aplication\Auth\UseCases;


use App\Aplication\Auth\DTOs\ProfileUserDto;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Exceptions\UserNotFoundException;

class ProfileUsecase
{

    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}


    public function execute(ProfileUserDto $dto): array
    {


        $user = $this->userRepository->findById($dto->user->id);

        if (!$user) {
            throw new UserNotFoundException();
        }


        return [
            'user' => $dto->user,
        ];
    }
}
