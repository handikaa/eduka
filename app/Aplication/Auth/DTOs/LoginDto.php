<?php

namespace App\Aplication\Auth\DTOs;

class LoginDto

{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}
