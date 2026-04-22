<?php

namespace App\Http\Controllers;

use App\Aplication\LessonProgress\UseCases\StartLessonProgressUsecase;
use App\Aplication\LessonProgress\UseCases\MarkLessonProgressAsCompletedUsecase;
use App\Aplication\LessonProgress\UseCases\GetStudentLessonProgressByCourseUsecase;
use App\Aplication\LessonProgress\UseCases\GetCourseProgressSummaryUsecase;
use App\Aplication\LessonProgress\UseCases\GetLessonProgressDetailUsecase;
use App\Aplication\LessonProgress\UseCases\UpdateLastAccessedLessonProgressUsecase;
use App\Aplication\LessonProgress\DTOs\StartLessonProgressDto;
use App\Aplication\LessonProgress\DTOs\GetLessonProgressDetailDto;
use App\Aplication\LessonProgress\DTOs\MarkLessonProgressAsCompletedDto;
use App\Aplication\LessonProgress\DTOs\GetStudentLessonProgressByCourseDto;
use App\Aplication\LessonProgress\DTOs\GetCourseProgressSummaryDto;
use App\Aplication\LessonProgress\DTOs\UpdateLastAccessedLessonProgressDto;
use App\Http\Responses\ApiResponse;
use app\Domain\LessonProgress\Exceptions\OnlyStudentCanStartLessonProgressException;
use app\Domain\LessonProgress\Exceptions\OnlyStudentCanUpdateLastAccessedLessonProgressException;
use app\Domain\Lessons\Exceptions\LessonNotFoundException;
use app\Domain\User\Exceptions\OnlyStudentCanAccessEnrollmentException;
use app\Domain\User\Exceptions\OnlyStudentCanCompleteLessonProgressException;
use Illuminate\Http\Request;
use Throwable;
use DomainException;

class LessonProgressController extends Controller
{
    //

    public function start(
        int $id,
        Request $request,
        StartLessonProgressUsecase $usecase
    ) {
        try {
            $student = $request->user();

            if (! $student) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new StartLessonProgressDto(
                lessonId: $id
            );

            $result = $usecase->execute(
                student: $student,
                dto: $dto
            );

            return ApiResponse::success(
                data: [
                    'id' => $result->id,
                    'user_id' => $result->user_id,
                    'lesson_id' => $result->lesson_id,
                    'status' => $result->status,
                    'completed_at' => $result->completed_at,
                    'last_accessed_at' => $result->last_accessed_at,
                ],
                message: 'Berhasil memulai progress lesson'
            );
        } catch (OnlyStudentCanStartLessonProgressException | OnlyStudentCanAccessEnrollmentException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (LessonNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal memulai progress lesson',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    public function complete(
        int $id,
        Request $request,
        MarkLessonProgressAsCompletedUsecase $usecase
    ) {
        try {
            $student = $request->user();

            if (! $student) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new MarkLessonProgressAsCompletedDto(
                lessonId: $id
            );

            $result = $usecase->execute(
                student: $student,
                dto: $dto
            );

            return ApiResponse::success(
                data: [
                    'id' => $result->id,
                    'user_id' => $result->user_id,
                    'lesson_id' => $result->lesson_id,
                    'status' => $result->status,
                    'completed_at' => $result->completed_at,
                    'last_accessed_at' => $result->last_accessed_at,
                ],
                message: 'Berhasil menyelesaikan lesson'
            );
        } catch (OnlyStudentCanCompleteLessonProgressException | OnlyStudentCanAccessEnrollmentException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (LessonNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (DomainException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal menyelesaikan lesson',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    public function indexByCourse(
        int $id,
        Request $request,
        GetStudentLessonProgressByCourseUsecase $usecase
    ) {
        try {
            $student = $request->user();

            if (! $student) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new GetStudentLessonProgressByCourseDto(
                courseId: $id
            );

            $result = $usecase->execute(
                student: $student,
                dto: $dto
            );

            return ApiResponse::success(
                data: $result,
                message: 'Berhasil mengambil progress lesson by course'
            );
        } catch (OnlyStudentCanAccessEnrollmentException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (DomainException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil progress lesson by course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    public function summaryByCourse(
        int $id,
        Request $request,
        GetCourseProgressSummaryUsecase $usecase
    ) {
        try {
            $student = $request->user();

            if (! $student) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new GetCourseProgressSummaryDto(
                courseId: $id
            );

            $result = $usecase->execute(
                student: $student,
                dto: $dto
            );

            return ApiResponse::success(
                data: $result,
                message: 'Berhasil mengambil summary progress course'
            );
        } catch (OnlyStudentCanAccessEnrollmentException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (DomainException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil summary progress course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    public function showDetail(
        int $id,
        Request $request,
        GetLessonProgressDetailUsecase $usecase
    ) {
        try {
            $student = $request->user();

            if (! $student) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new GetLessonProgressDetailDto(
                lessonId: $id
            );

            $result = $usecase->execute(
                student: $student,
                dto: $dto
            );

            return ApiResponse::success(
                data: $result,
                message: 'Berhasil mengambil detail progress lesson'
            );
        } catch (OnlyStudentCanAccessEnrollmentException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (LessonNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (DomainException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil detail progress lesson',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    public function updateLastAccessed(
        int $id,
        Request $request,
        UpdateLastAccessedLessonProgressUsecase $usecase
    ) {
        try {
            $student = $request->user();

            if (! $student) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new UpdateLastAccessedLessonProgressDto(
                lessonId: $id
            );

            $result = $usecase->execute(
                student: $student,
                dto: $dto
            );

            return ApiResponse::success(
                data: [
                    'id' => $result->id,
                    'user_id' => $result->user_id,
                    'lesson_id' => $result->lesson_id,
                    'status' => $result->status,
                    'completed_at' => $result->completed_at,
                    'last_accessed_at' => $result->last_accessed_at,
                ],
                message: 'Berhasil memperbarui last accessed lesson progress'
            );
        } catch (OnlyStudentCanUpdateLastAccessedLessonProgressException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (LessonNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (DomainException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal memperbarui last accessed lesson progress',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }
}
