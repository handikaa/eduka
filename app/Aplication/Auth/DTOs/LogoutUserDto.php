<?php

namespace App\Aplication\Auth\DTOs;

use App\Infrastructure\Persistance\Eloquent\Models\User;

class LogoutUserDto
{
    public function __construct(
        public User $user
    ) {}
}
