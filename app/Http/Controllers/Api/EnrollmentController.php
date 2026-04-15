<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Aplication\Enrollment\UseCases\EnrollStudentUsecase;
use App\Aplication\Enrollment\UseCases\GetStudentEnrollmentsUsecase;
use App\Aplication\Enrollment\UseCases\GetCourseEnrollmentsUsecase;
use App\Aplication\Enrollment\UseCases\GetEnrollmentDetailUsecase;
use App\Aplication\Enrollment\DTOs\EnrollStudentDto;
use App\Aplication\Enrollment\DTOs\GetStudentEnrollmentsDto;
use App\Aplication\Enrollment\DTOs\GetCourseEnrollmentsDto;
use App\Aplication\Enrollment\DTOs\GetEnrollmentDetailDto;
use App\Http\Responses\ApiResponse;
use App\Http\Resources\EnrollmentStudentResource;
use App\Domain\User\Exceptions\OnlyStudentCanEnrollCourseException;
use App\Domain\User\Exceptions\StudentAlreadyEnrollException;
use App\Domain\User\Exceptions\OnlyStudentCanAccessEnrollmentException;
use App\Domain\Courses\Exceptions\CourseNotFoundException;
use App\Domain\Enrollment\Exceptions\CourseQuotaIsFullException;
use App\Domain\Enrollment\Exceptions\OnlyInstructorCanAccessCourseEnrollmentsException;
use App\Domain\Enrollment\Exceptions\InstructorCannotAccessOtherCourseEnrollmentsException;
use App\Domain\Enrollment\Exceptions\EnrollmentNotFoundException;
use App\Domain\Enrollment\Exceptions\UnauthorizedToViewEnrollmentDetailException;
use App\Http\Resources\EnrollmentListResource;
use App\Http\Resources\CourseEnrollmentListResource;
use App\Http\Resources\EnrollmentDetailResource;
use Throwable;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(
        int $id,
        EnrollStudentUsecase $usecase,
        Request $request
    ) {
        try {
            $dto = new EnrollStudentDto(
                courseId: $id
            );

            $result = $usecase->execute(
                dto: $dto,
                student: $request->user()
            );

            return ApiResponse::success(
                data: new EnrollmentStudentResource($result),
                message: 'Berhasil enroll course',
                code: 201
            );
        } catch (OnlyStudentCanEnrollCourseException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (StudentAlreadyEnrollException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 409
            );
        } catch (CourseQuotaIsFullException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 409
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal enroll course',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    public function indexMyEnrollments(
        Request $request,
        GetStudentEnrollmentsUsecase $usecase
    ) {
        try {
            $dto = new GetStudentEnrollmentsDto(
                perPage: (int) ($request->query('per_page', 10))
            );

            $result = $usecase->execute(
                student: $request->user(),
                dto: $dto
            );

            return ApiResponse::successPaginated(
                data: EnrollmentListResource::collection($result->items()),
                message: 'Berhasil mengambil daftar enrollment',
                pagination: [
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'per_page' => $result->perPage(),
                    'total' => $result->total(),
                ]
            );
        } catch (OnlyStudentCanAccessEnrollmentException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil daftar enrollment',
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
        GetCourseEnrollmentsUsecase $usecase
    ) {
        try {
            $instructor = $request->user();

            if (! $instructor) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new GetCourseEnrollmentsDto(
                courseId: $id,
                perPage: (int) ($request->query('per_page', 10))
            );

            $result = $usecase->execute(
                instructor: $instructor,
                dto: $dto
            );

            return ApiResponse::successPaginated(
                data: CourseEnrollmentListResource::collection($result->items()),
                pagination: [
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'per_page' => $result->perPage(),
                    'total' => $result->total(),
                ],
                message: 'Berhasil mengambil daftar enrollment course'
            );
        } catch (OnlyInstructorCanAccessCourseEnrollmentsException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (InstructorCannotAccessOtherCourseEnrollmentsException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (CourseNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil daftar enrollment',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    public function show(
        int $id,
        Request $request,
        GetEnrollmentDetailUsecase $usecase
    ) {
        try {
            $authUser = $request->user();

            if (! $authUser) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new GetEnrollmentDetailDto(
                enrollmentId: $id
            );

            $result = $usecase->execute(
                authUser: $authUser,
                dto: $dto
            );

            return ApiResponse::success(
                data: new EnrollmentDetailResource($result),
                message: 'Berhasil mengambil detail enrollment'
            );
        } catch (EnrollmentNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (UnauthorizedToViewEnrollmentDetailException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil daftar enrollment',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }
}
