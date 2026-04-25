<?php

namespace App\Aplication\Users\DTOs;

class GetUsersDto
{
    public function __construct(
        public readonly int $perPage = 10,
        public readonly int $page = 1,
        public readonly ?string $role = null,
        public readonly ?string $search = null,
    ) {}
}