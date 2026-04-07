<?php

namespace App\Aplication\Auth\Services;

use App\Infrastructure\Persistance\Eloquent\Models\User;

class TokenIssuer
{
    public function issue(User $user, string $tokenName = 'auth_token'): string
    {
        // Implement token generation logic here, e.g., using JWT or Laravel Sanctum
        return $user->createToken($tokenName)->plainTextToken;
    }
}

