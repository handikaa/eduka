<?php

namespace App\Infrastructure\Persistance\Eloquent\Repositories;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

     public function getAll(
        int $perPage = 10,
        int $page = 1,
        ?string $role = null,
        ?string $search = null
    ): LengthAwarePaginator {
        $query = User::query();

        if ($role) {
            $query->where('role', $role);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query
            ->latest('id')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}