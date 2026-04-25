<?php

namespace App\Domain\User\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

use App\Infrastructure\Persistance\Eloquent\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function update(User $user, array $data): User;

    public function delete(User $user): void;

    public function logout(User $user): void;

    public function getAll(int $perPage = 10, int $page = 1, ?string $role = null, ?string $search = null): LengthAwarePaginator;
}
