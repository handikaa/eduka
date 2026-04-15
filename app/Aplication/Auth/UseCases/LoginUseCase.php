<?php

namespace App\Aplication\Auth\UseCases;

use App\Aplication\Auth\DTOs\LoginDto;
use App\Aplication\Auth\Services\TokenIssuer;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Aplication\Auth\Services\PasswordHasher;
use App\Domain\Auth\Exceptions\InvalidCredentialsException;

class LoginUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasher $passwordHasher,
        private TokenIssuer $tokenIssuer,
    ) {
        $this->tokenIssuer = $tokenIssuer;
    }

    public function execute(LoginDto $dto): array
    {
        // Implement login logic here, e.g., validate credentials and retrieve user
        $user = $this->userRepository->findByEmail($dto->email);

        if (!$user || !$this->passwordHasher->check($dto->password, $user->password_hash)) {
            throw new InvalidCredentialsException();
        }

        // Issue a token for the authenticated user
        $token = $this->tokenIssuer->issue($user);

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}
