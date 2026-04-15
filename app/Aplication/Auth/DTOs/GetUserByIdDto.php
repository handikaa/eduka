<?php

namespace App\Aplication\Auth\DTOs;

class GetUserByIdDto

{
    public function __construct(
        public int $id
    ) {}
}
