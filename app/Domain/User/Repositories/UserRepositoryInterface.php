<?php

namespace App\Domain\User\Repositories;

use App\Infrastructure\Persistance\Eloquent\Models\User;
interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function update(User $user, array $data): User;

    public function delete(User $user): void;
}