<?php

namespace App\Aplication\Courses\Services;

use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\Courses\Repositories\LessonRepositoryInterface;
use App\Aplication\Courses\DTOs\CreateCourseDto;
use App\Aplication\Courses\DTOs\CreateLessonDto;
use App\Aplication\Courses\UseCases\CreateCourseUsecase;
use App\Aplication\Courses\UseCases\CreateLessonsUsecase;
use Illuminate\Support\Facades\DB;

class CreateCourseWithLessonsService
{
    public function __construct(
        private CreateCourseUsecase $createCourseUsecase,
        private CreateLessonsUsecase $createLessonsUsecase
    ) {}

    public function execute(CreateCourseDto $courseDto, array $lessons)
    {
        return DB::transaction(function () use ($courseDto, $lessons) {
            $course = $this->createCourseUsecase->execute($courseDto);

            foreach ($lessons as $index => $lesson) {
                $lessonDto = new CreateLessonDto(
                    courseId: $course->id,
                    title: $lesson['title'],
                    content: $lesson['content'] ?? null,
                    type: $lesson['type'],
                    videoUrl: $lesson['video_url'] ?? null,
                    isPreview: filter_var($lesson['is_preview'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    position: $index + 1
                );

                $this->createLessonsUsecase->execute($lessonDto);
            }

            return $course->load(['categories', 'lessons']);
        });
    }
}
