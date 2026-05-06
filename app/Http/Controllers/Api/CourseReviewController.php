<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;
use App\Http\Responses\ApiResponse;
use App\Aplication\CourseReview\DTOs\CreateCourseReviewDto;
use App\Aplication\CourseReview\DTOs\GetCourseReviewsBySlugDto;
use App\Aplication\CourseReview\DTOs\GetCourseReviewsDto;
use App\Aplication\CourseReview\DTOs\GetStudentCourseReviewDto;
use App\Aplication\CourseReview\DTOs\UpdateCourseReviewDto;
use App\Aplication\CourseReview\UseCases\CreateCourseReviewUsecase;
use App\Aplication\CourseReview\UseCases\GetCourseReviewsByCourseSlugUsecase;
use App\Aplication\CourseReview\UseCases\GetCourseReviewsUsecase;
use App\Aplication\CourseReview\UseCases\RestoreCourseReviewUsecase;
use App\Aplication\CourseReview\UseCases\DeleteCourseReviewUsecase;
use App\Aplication\CourseReview\UseCases\UpdateCourseReviewUsecase;
use App\Aplication\CourseReview\UseCases\GetStudentCourseReviewUsecase;
use App\Domain\Courses\Exceptions\CourseNotFoundException;
use App\Domain\CourseReview\Exceptions\InvalidCourseReviewRatingException;
use App\Domain\CourseReview\Exceptions\OnlyStudentCanCreateCourseReviewException;
use App\Domain\CourseReview\Exceptions\StudentAlreadyReviewedCourseException;
use App\Domain\CourseReview\Exceptions\CourseReviewNotFoundException;
use App\Domain\CourseReview\Exceptions\DeletedCourseReviewNotFoundException;
use App\Domain\CourseReview\Exceptions\StudentMustCompleteCourseBeforeReviewException;
use App\Domain\CourseReview\Exceptions\StudentCourseReviewNotFoundException;

class CourseReviewController extends Controller
{
    //

    public function store(
        int $id,
        Request $request,
        CreateCourseReviewUsecase $usecase
    ) {
        try {
            $student = $request->user();

            if (! $student) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new CreateCourseReviewDto(
                courseId: $id,
                rating: (int) $request->input('rating'),
                comment: $request->input('comment')
            );

            $result = $usecase->execute(
                student: $student,
                dto: $dto
            );

            return ApiResponse::success(
                data: [
                    'id' => $result->id,
                    'course_id' => $result->course_id,
                    'user_id' => $result->user_id,
                    'rating' => $result->rating,
                    'comment' => $result->comment,
                    'is_delete' => $result->is_delete,
                    'created_at' => $result->created_at,
                    'updated_at' => $result->updated_at,
                ],
                message: 'Berhasil membuat review course',
                code: 201
            );
        } catch (OnlyStudentCanCreateCourseReviewException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (InvalidCourseReviewRatingException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 422
            );
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (StudentMustCompleteCourseBeforeReviewException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (StudentAlreadyReviewedCourseException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 409
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal membuat review course',
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
        GetCourseReviewsUsecase $usecase
    ) {
        try {
            $dto = new GetCourseReviewsDto(
                courseId: $id,
                perPage: (int) ($request->query('per_page', 10)),
                page: (int) ($request->query('page', 1))
            );

            $result = $usecase->execute($dto);

            return ApiResponse::successPaginated(
                data: $result->items(),
                pagination: [
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'per_page' => $result->perPage(),
                    'total' => $result->total(),
                ],
                message: 'Berhasil mengambil daftar review course'
            );
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil daftar review course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    public function indexByCourseSlug(
        string $slug,
        Request $request,
        GetCourseReviewsByCourseSlugUsecase $usecase
    ) {
        try {
            $dto = new GetCourseReviewsBySlugDto(
                courseSlug: $slug,
                perPage: (int) ($request->query('per_page', 10)),
                page: (int) ($request->query('page', 1))
            );

            $result = $usecase->execute($dto);

            return ApiResponse::successPaginated(
                data: $result->items(),
                pagination: [
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'per_page' => $result->perPage(),
                    'total' => $result->total(),
                ],
                message: 'Berhasil mengambil daftar review course berdasarkan slug'
            );
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil daftar review course berdasarkan slug',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    public function showStudentReviewByCourse(
        int $id,
        Request $request,
        GetStudentCourseReviewUsecase $usecase
    ) {
        try {
            $student = $request->user();

            if (! $student) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new GetStudentCourseReviewDto(
                courseId: $id
            );

            $result = $usecase->execute(
                student: $student,
                dto: $dto
            );

            return ApiResponse::success(
                data: [
                    'id' => $result->id,
                    'course_id' => $result->course_id,
                    'user_id' => $result->user_id,
                    'rating' => $result->rating,
                    'comment' => $result->comment,
                    'is_delete' => $result->is_delete,
                    'created_at' => $result->created_at,
                    'updated_at' => $result->updated_at,
                ],
                message: 'Berhasil mengambil review student pada course'
            );
        } catch (OnlyStudentCanCreateCourseReviewException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (StudentCourseReviewNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil review student pada course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }
    public function updateByCourse(
        int $id,
        Request $request,
        UpdateCourseReviewUsecase $usecase
    ) {
        try {
            $student = $request->user();

            if (! $student) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new UpdateCourseReviewDto(
                courseId: $id,
                rating: (int) $request->input('rating'),
                comment: $request->input('comment')
            );

            $result = $usecase->execute(
                student: $student,
                dto: $dto
            );

            return ApiResponse::success(
                data: [
                    'id' => $result->id,
                    'course_id' => $result->course_id,
                    'user_id' => $result->user_id,
                    'rating' => $result->rating,
                    'comment' => $result->comment,
                    'is_delete' => $result->is_delete,
                    'created_at' => $result->created_at,
                    'updated_at' => $result->updated_at,
                ],
                message: 'Berhasil memperbarui review course'
            );
        } catch (OnlyStudentCanCreateCourseReviewException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (InvalidCourseReviewRatingException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 422
            );
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (CourseReviewNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal memperbarui review course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    public function deleteByCourse(
        int $id,
        Request $request,
        DeleteCourseReviewUsecase $usecase
    ) {
        try {
            $student = $request->user();

            if (! $student) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $result = $usecase->execute(
                student: $student,
                courseId: $id
            );

            return ApiResponse::success(
                data: [
                    'id' => $result->id,
                    'course_id' => $result->course_id,
                    'user_id' => $result->user_id,
                    'rating' => $result->rating,
                    'comment' => $result->comment,
                    'is_delete' => $result->is_delete,
                    'created_at' => $result->created_at,
                    'updated_at' => $result->updated_at,
                ],
                message: 'Berhasil menghapus review course'
            );
        } catch (OnlyStudentCanCreateCourseReviewException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (CourseReviewNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal menghapus review course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    public function restoreByCourse(
        int $id,
        Request $request,
        RestoreCourseReviewUsecase $usecase
    ) {
        try {
            $student = $request->user();

            if (! $student) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $result = $usecase->execute(
                student: $student,
                courseId: $id
            );

            return ApiResponse::success(
                data: [
                    'id' => $result->id,
                    'course_id' => $result->course_id,
                    'user_id' => $result->user_id,
                    'rating' => $result->rating,
                    'comment' => $result->comment,
                    'is_delete' => $result->is_delete,
                    'created_at' => $result->created_at,
                    'updated_at' => $result->updated_at,
                ],
                message: 'Berhasil me-restore review course'
            );
        } catch (OnlyStudentCanCreateCourseReviewException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (DeletedCourseReviewNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal me-restore review course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }
}
