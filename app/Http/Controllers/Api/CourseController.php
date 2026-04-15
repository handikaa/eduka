<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Aplication\Courses\DTOs\CreateCourseDto;
use App\Aplication\Courses\DTOs\UpdateCourseDTO;
use App\Aplication\Courses\DTOs\UpdateCourseStatusDto;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseStatusRequest;
use App\Http\Resources\CourseResource;
use App\Aplication\Courses\UseCases\GetAllCoursesUsecase;
use App\Aplication\Courses\UseCases\GetCourseByIdUsecase;
use App\Aplication\Courses\UseCases\GetCourseBySlugUsecase;
use App\Aplication\Courses\UseCases\DeleteCourseUsecase;
use App\Aplication\Courses\UseCases\ForceDeleteCourseUsecase;
use App\Aplication\Courses\UseCases\RestoreCourseUsecase;
use  App\Aplication\Courses\UseCases\UpdateCourseUsecase;
use  App\Aplication\Courses\UseCases\UpdateCourseStatusUsecase;
use App\Aplication\Courses\Services\CreateCourseWithLessonsService;
use App\Domain\User\Exceptions\OnlyMentorCanCreateCourseException;
use App\Domain\Courses\Exceptions\CourseNotFoundException;
use App\Domain\Courses\Exceptions\CategoryIsRequiredException;
use App\Domain\Courses\Exceptions\UnauthorizedCourseAccessException;
use App\Domain\Courses\Exceptions\InvalidCourseStatusException;
use Throwable;

class CourseController extends Controller
{
    /**
     * Get all courses with instructor and category info
     * GET /api/v1/courses
     */
    public function index(GetAllCoursesUsecase $usecase)
    {
        $courses = $usecase->execute();

        return ApiResponse::success(
            CourseResource::collection($courses),
            'List courses'
        );
    }


    /**
     * Get course detail by ID
     * GET /api/v1/courses/{id}
     */
    public function show(int $id, GetCourseByIdUsecase $usecase)
    {
        try {
            $course = $usecase->execute($id);

            return ApiResponse::success(
                new CourseResource($course),
                'Detail course'
            );
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal membuat course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage()
                ]
            );
        }
    }
    public function showBySlug(string $slug, GetCourseBySlugUsecase $usecase)
    {
        try {
            $course = $usecase->execute($slug);

            return ApiResponse::success(
                new CourseResource($course),
                'Detail course'
            );
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal membuat course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage()
                ]
            );
        }
    }


    public function store(StoreCourseRequest $request, CreateCourseWithLessonsService $service,)
    {
        try {
            $user = $request->user();

            $courseDto = new CreateCourseDto(
                instructorId: $user->id,
                title: $request->title,
                description: $request->description,
                level: $request->level,
                price: $request->price,
                quota: $request->quota,
                categoryIds: $request->input('categories', []),
                thumbnailUrl: $request->thumbnail_url
            );

            $course = $service->execute(
                $courseDto,
                $request->lessons ?? []
            );

            return ApiResponse::success(
                new CourseResource($course),
                'Course & lessons created successfully'
            );
        } catch (OnlyMentorCanCreateCourseException $e) {
            return ApiResponse::error($e->getMessage(), 403);
        } catch (CategoryIsRequiredException $e) {
            return ApiResponse::error($e->getMessage(), 422);
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal membuat course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage()
                ]
            );
        }
    }

    public function updateStatus(
        string $slug,
        UpdateCourseStatusRequest $request,
        UpdateCourseStatusUsecase $usecase
    ) {
        try {
            $dto = new UpdateCourseStatusDto(
                status: $request->validated('status')
            );

            $course = $usecase->execute(
                $request->user(),
                $slug,
                $dto
            );

            return ApiResponse::success(
                new CourseResource($course),
                'Status course berhasil diperbarui'
            );
        } catch (UnauthorizedCourseAccessException $e) {
            return ApiResponse::error($e->getMessage(), 403);
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (InvalidCourseStatusException $e) {
            return ApiResponse::error($e->getMessage(), 422);
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal memperbarui status course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage()
                ]
            );
        }
    }

    public function update(int $id, Request $request, UpdateCourseUsecase $usecase)
    {
        try {
            $dto = UpdateCourseDTO::fromRequest($request);

            $course = $usecase->execute(
                $request->user(),
                $id,
                $dto
            );

            return ApiResponse::success(
                new CourseResource($course),
                'Course updated'
            );
        } catch (UnauthorizedCourseAccessException $e) {
            return ApiResponse::error($e->getMessage(), 403);
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengupdate course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage()
                ]
            );
        }
    }


    /**
     * Delete course (Only Owner/Instructor) - Soft Delete
     * DELETE /api/v1/courses/{id}
     */
    public function destroy(int $id, DeleteCourseUsecase $usecase)
    {
        try {
            $usecase->execute($id);

            return ApiResponse::success(null, 'Course berhasil dihapus');
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal membuat course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage()
                ]
            );
        }
    }


    /**
     * Restore soft deleted course (Only Owner/Instructor)
     * POST /api/v1/courses/{id}/restore
     */
    public function restore(int $id, RestoreCourseUsecase $usecase)
    {
        try {
            $usecase->execute($id);

            return ApiResponse::success(null, 'Course berhasil direstore');
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal membuat course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Permanently delete a soft-deleted course (Only Owner/Instructor)
     * DELETE /api/v1/courses/{id}/force
     */
    public function forceDelete(int $id, ForceDeleteCourseUsecase $usecase)
    {
        try {
            $usecase->execute($id);

            return ApiResponse::success(null, 'Course dihapus permanen');
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal membuat course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage()
                ]
            );
        }
    }
}
