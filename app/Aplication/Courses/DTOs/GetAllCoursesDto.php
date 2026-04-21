<?php

namespace App\Aplication\Courses\DTOs;

use Illuminate\Http\Request;

class GetAllCoursesDto
{
    public function __construct(
        public int $page = 1,
        public int $perPage = 10,
        public ?int $userId = null,
        public ?string $status = null,
        public ?string $level = null,
        public ?int $categoryId = null,
        public ?string $search = null,
        public string $sortBy = 'created_at',
        public string $sortDirection = 'desc',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            page: max((int) $request->query('page', 1), 1),
            perPage: max((int) $request->query('per_page', 10), 1),
            userId: $request->filled('user_id') ? (int) $request->query('user_id') : null,
            status: $request->query('status'),
            level: $request->query('level'),
            categoryId: $request->filled('category_id') ? (int) $request->query('category_id') : null,
            search: $request->query('search'),
            sortBy: $request->query('sort_by', 'created_at'),
            sortDirection: strtolower($request->query('sort_direction', 'desc')),
        );
    }
}
