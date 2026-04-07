<?php

namespace App\Aplication\Auth\UseCases;

use App\Aplication\Auth\DTOs\RegisterDto;
use App\Aplication\Auth\Services\TokenIssuer;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Aplication\Auth\Services\PasswordHasher;
use App\Domain\Auth\Exceptions\UserAlreadyExistsException;


class RegisterUseCase
{

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasher $passwordHasher,
        private TokenIssuer $tokenIssuer,
    ) {}

    public function execute(RegisterDto $dto): array
    {
        $existingUser = $this->userRepository->findByEmail($dto->email);

        if ($existingUser) {
            throw new UserAlreadyExistsException();
        }

        $user = $this->userRepository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password_hash' => $this->passwordHasher->hash($dto->password),
            'role' => $dto->role,
            'is_active' => true,
            'avatar_url' => null,
        ]);

        $token = $this->tokenIssuer->issue($user);

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}
