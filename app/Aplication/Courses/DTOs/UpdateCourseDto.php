<?php

namespace App\Aplication\Courses\DTOs;

class UpdateCourseDTO
{
    public function __construct(
        public string $title,
        public string $level,
        public int $price,
        public int $quota,
        public ?string $description,
        public ?string $thumbnail_url,
        public ?string $status,
        public ?array $lessons,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            title: $request->title,
            level: $request->level,
            price: $request->price,
            quota: $request->quota,
            description: $request->description,
            thumbnail_url: $request->thumbnail_url,
            status: $request->status,
            lessons: collect($request->lessons ?? [])
                ->map(fn($lesson) => new LessonDTO(
                    id: $lesson['id'] ?? null,
                    title: $lesson['title'],
                    type: $lesson['type'],
                    content: $lesson['content'],
                    video_url: $lesson['video_url'] ?? null,
                    is_preview: $lesson['is_preview'] ?? false,
                ))->toArray()
        );
    }
}
