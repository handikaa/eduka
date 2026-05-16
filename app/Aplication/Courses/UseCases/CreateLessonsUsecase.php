<?php


namespace App\Aplication\Courses\UseCases;

use App\Domain\Courses\Repositories\LessonRepositoryInterface;
use App\Aplication\Courses\DTOs\CreateLessonDto;
use App\Infrastructure\Persistance\Eloquent\Models\Lesson;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\Courses\Exceptions\CourseNotFoundException;

class CreateLessonsUsecase
{
    public function __construct(
        private LessonRepositoryInterface $lessonRepository,
        private CourseRepositoryInterface $courseRepository
    ) {}

    public function execute(CreateLessonDto $dto): Lesson
    {
        $course = $this->courseRepository->findById($dto->courseId);

        if (!$course) {
            throw new CourseNotFoundException();
        }

        return $this->lessonRepository->create([
            'course_id' => $dto->courseId,
            'title' => $dto->title,
            'content' => $dto->content,
            'type' => $dto->type,
            'video_url' => $dto->videoUrl,
            'file_url' => $dto->fileUrl,
            'is_preview' => $dto->isPreview,
            'position' => $dto->position,
        ]);
    }
}
