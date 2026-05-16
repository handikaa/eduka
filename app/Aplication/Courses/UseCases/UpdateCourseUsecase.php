<?php

namespace App\Aplication\Courses\UseCases;

use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\Courses\Exceptions\CourseNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Aplication\Courses\DTOs\UpdateCourseDTO;
use App\Aplication\Courses\DTOs\LessonDTO;
use App\Domain\Courses\Exceptions\UnauthorizedCourseAccessException;



class UpdateCourseUsecase
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository
    ) {}

    public function execute(object $user, int $id, UpdateCourseDTO $dto)

    {
        return DB::transaction(function () use ($user, $id, $dto) {
            if (!$user || $user->role !== 'instructor') {
                throw new UnauthorizedCourseAccessException();
            }

            $course = $this->courseRepository->findById($id);

            if (!$course) {
                throw new CourseNotFoundException();
            }

            if ((int) $course->instructor_id !== (int) $user->id) {
                throw new UnauthorizedCourseAccessException();
            }

            $course = $this->courseRepository->update($course, [
                'title' => $dto->title,
                'level' => $dto->level,
                'price' => $dto->price,
                'quota' => $dto->quota,
                'description' => $dto->description,
                'thumbnail_url' => $dto->thumbnail_url,
                'status' => $dto->status,
            ]);

            if ($dto->lessons) {
                $this->syncLessons($course, $dto->lessons);
            }

            return $course->load('lessons');
        });
    }

    private function syncLessons($course, array $lessons)
    {
        foreach ($lessons as $index => $lessonDTO) {
            $payload = [
                'title' => $lessonDTO->title,
                'type' => $lessonDTO->type,
                'content' => $lessonDTO->content,
                'video_url' => $lessonDTO->video_url,
                'file_url' => $lessonDTO->file_url,
                'is_preview' => $lessonDTO->is_preview,
                'position' => $lessonDTO->position ?? $index + 1,
            ];

            if ($lessonDTO->id) {
                $course->lessons()
                    ->where('id', $lessonDTO->id)
                    ->update($payload);

                continue;
            }

            $course->lessons()->create($payload);
        }
    }
}
