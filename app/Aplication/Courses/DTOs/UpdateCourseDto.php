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

    public static function fromRequest($request, ?array $lessons = null, ?string $thumbnailUrl = null): self
    {
        $resolvedLessons = $lessons ?? $request->input('lessons', []);

        return new self(
            title: $request->title,
            level: $request->level,
            price: (int) $request->price,
            quota: (int) $request->quota,
            description: $request->description,
            thumbnail_url: $thumbnailUrl ?? $request->thumbnail_url,
            status: $request->status,
            lessons: collect($resolvedLessons)
                ->map(fn ($lesson, $index) => new LessonDTO(
                    id: isset($lesson['id']) ? (int) $lesson['id'] : null,
                    title: $lesson['title'],
                    type: $lesson['type'],
                    content: $lesson['content'] ?? null,
                    video_url: $lesson['video_url'] ?? null,
                    file_url: $lesson['file_url'] ?? null,
                    is_preview: filter_var(
                        $lesson['is_preview'] ?? false,
                        FILTER_VALIDATE_BOOLEAN
                    ),
                    position: isset($lesson['position'])
                        ? (int) $lesson['position']
                        : $index + 1,
                ))->toArray()
        );
    }
}